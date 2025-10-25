<?php
require_once 'config.php';
require_once 'ErrorHandler.php';

/**
 * Audio Analysis Class using Gemini API
 */
class AudioAnalyzer {
    private $apiKeys;
    private $currentKeyIndex = 0;
    private $logFile;
    
    public function __construct() {
        $this->apiKeys = GEMINI_API_KEYS;
        $this->logFile = LOG_FILE;
        
        // Initialize error handler now that config is loaded
        ErrorHandler::getInstance();
    }
    
    /**
     * Log messages with timestamp
     */
    private function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get next API key for rotation
     */
    private function getNextApiKey() {
        $key = $this->apiKeys[$this->currentKeyIndex];
        $this->currentKeyIndex = ($this->currentKeyIndex + 1) % count($this->apiKeys);
        return $key;
    }
    
    /**
     * Make API request to Gemini
     */
    private function makeGeminiRequest($prompt, $audioData = null) {
        $maxRetries = count($this->apiKeys);
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            $apiKey = $this->getNextApiKey();
            $url = GEMINI_API_URL . "?key=" . $apiKey;
            
            $requestData = [
                'contents' => [
                    [
                        'parts' => []
                    ]
                ]
            ];
            
            // Add audio data if provided
            if ($audioData) {
                $requestData['contents'][0]['parts'][] = [
                    'inline_data' => [
                        'mime_type' => $audioData['mime_type'],
                        'data' => $audioData['data']
                    ]
                ];
            }
            
            // Add text prompt
            $requestData['contents'][0]['parts'][] = [
                'text' => $prompt
            ];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode($requestData),
                CURLOPT_TIMEOUT => 120,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                $this->log("CURL Error with API key " . substr($apiKey, 0, 10) . "...: $error", 'ERROR');
                $retryCount++;
                continue;
            }
            
            if ($httpCode === 200) {
                $decodedResponse = json_decode($response, true);
                if (isset($decodedResponse['candidates'][0]['content']['parts'][0]['text'])) {
                    $this->log("Successful API request with key " . substr($apiKey, 0, 10) . "...");
                    return $decodedResponse['candidates'][0]['content']['parts'][0]['text'];
                } else {
                    $this->log("Invalid response structure: " . $response, 'ERROR');
                }
            } else {
                $this->log("HTTP Error $httpCode with API key " . substr($apiKey, 0, 10) . "...: $response", 'ERROR');
            }
            
            $retryCount++;
            sleep(1); // Brief delay before retry
        }
        
        throw new Exception("All API keys failed. Unable to process request.");
    }
    
    /**
     * Upload and validate audio file
     */
    public function uploadAudio($file) {
        try {
            // Validate file upload
            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                throw new Exception("No valid file uploaded");
            }
            
            // Check file size
            if ($file['size'] > MAX_FILE_SIZE) {
                throw new Exception("File size exceeds maximum limit of " . (MAX_FILE_SIZE / 1024 / 1024) . "MB");
            }
            
            // Check MIME type with multiple methods
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            // Fallback: check file extension if MIME type detection fails or is generic
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $extensionMimeMap = [
                'mp3' => 'audio/mpeg',
                'wav' => 'audio/wav',
                'm4a' => 'audio/m4a',
                'aac' => 'audio/aac',
                'ogg' => 'audio/ogg',
                'webm' => 'audio/webm',
                'flac' => 'audio/flac'
            ];
            
            // If MIME type is generic or unknown, try to determine from extension
            if ($mimeType === 'application/octet-stream' || !$mimeType) {
                if (isset($extensionMimeMap[$extension])) {
                    $mimeType = $extensionMimeMap[$extension];
                    $this->log("MIME type determined from extension: $extension -> $mimeType");
                }
            }
            
            if (!in_array($mimeType, ALLOWED_AUDIO_TYPES)) {
                $allowedFormats = implode(', ', array_unique(array_values($extensionMimeMap)));
                throw new Exception("Unsupported audio format: $mimeType. Supported formats: $allowedFormats. File extension: .$extension");
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('audio_') . '.' . $extension;
            $filepath = UPLOAD_DIR . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception("Failed to save uploaded file");
            }
            
            $this->log("Audio file uploaded successfully: $filename");
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'mime_type' => $mimeType,
                'size' => $file['size']
            ];
            
        } catch (Exception $e) {
            $this->log("Upload error: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Transcribe audio with timestamps
     */
    public function transcribeAudio($filepath, $mimeType) {
        try {
            $audioData = base64_encode(file_get_contents($filepath));
            
            $prompt = "Please transcribe this audio recording in its original language. Include timestamps for each segment of speech. Format the output as follows:

[HH:MM:SS] Speaker: Transcribed text
[HH:MM:SS] Speaker: Transcribed text

If you can identify different speakers, label them as Speaker 1, Speaker 2, etc. If not, just use 'Speaker' for all segments.

Provide accurate timestamps and complete transcription of all spoken content.";
            
            $result = $this->makeGeminiRequest($prompt, [
                'mime_type' => $mimeType,
                'data' => $audioData
            ]);
            
            $this->log("Audio transcription completed");
            return [
                'success' => true,
                'transcription' => $result
            ];
            
        } catch (Exception $e) {
            $this->log("Transcription error: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Translate transcription to English
     */
    public function translateToEnglish($transcription) {
        try {
            $prompt = "Please translate the following transcription to English while preserving the timestamp format and speaker labels:

$transcription

Maintain the same format:
[HH:MM:SS] Speaker: Translated text in English

If the original is already in English, return it as-is.";
            
            $result = $this->makeGeminiRequest($prompt);
            
            $this->log("Translation completed");
            return [
                'success' => true,
                'translation' => $result
            ];
            
        } catch (Exception $e) {
            $this->log("Translation error: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Perform comprehensive sentiment analysis
     */
    public function analyzeSentiment($originalTranscription, $englishTranslation) {
        try {
            $prompt = "Perform a detailed sentiment analysis of this conversation. Use both the original transcription and English translation for context:

ORIGINAL TRANSCRIPTION:
$originalTranscription

ENGLISH TRANSLATION:
$englishTranslation

Please provide a comprehensive analysis in the following JSON format:

{
    \"speaker_analysis\": [
        {
            \"speaker\": \"Speaker 1\",
            \"sentiment\": \"positive/neutral/negative\",
            \"confidence\": 0.85,
            \"reasoning\": \"Detailed explanation of sentiment indicators\",
            \"key_emotions\": [\"emotion1\", \"emotion2\"],
            \"tone_indicators\": [\"indicator1\", \"indicator2\"]
        }
    ],
    \"overall_sentiment\": {
        \"primary_sentiment\": \"positive/neutral/negative\",
        \"emotional_tone\": \"Description of overall emotional atmosphere\",
        \"empathy_level\": \"high/medium/low\",
        \"politeness_level\": \"high/medium/low\",
        \"frustration_indicators\": [\"indicator1\", \"indicator2\"],
        \"positive_indicators\": [\"indicator1\", \"indicator2\"]
    },
    \"sentiment_score\": {
        \"numerical_score\": 75,
        \"scale\": \"0-100 (0=very negative, 50=neutral, 100=very positive)\",
        \"confidence\": 0.88
    },
    \"agent_performance\": {
        \"clarity_score\": 85,
        \"empathy_score\": 78,
        \"professionalism_score\": 92,
        \"overall_performance\": \"good/excellent/needs_improvement\",
        \"strengths\": [\"strength1\", \"strength2\"],
        \"areas_for_improvement\": [\"area1\", \"area2\"],
        \"recommendations\": [\"recommendation1\", \"recommendation2\"]
    }
}

Provide only the JSON response, no additional text.";
            
            $result = $this->makeGeminiRequest($prompt);
            
            // Clean and validate JSON
            $result = trim($result);
            if (strpos($result, '```json') !== false) {
                $result = preg_replace('/```json\s*/', '', $result);
                $result = preg_replace('/```\s*$/', '', $result);
            }
            
            $analysisData = json_decode($result, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON response from sentiment analysis");
            }
            
            $this->log("Sentiment analysis completed");
            return [
                'success' => true,
                'analysis' => $analysisData
            ];
            
        } catch (Exception $e) {
            $this->log("Sentiment analysis error: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Process complete audio analysis pipeline
     */
    public function processAudio($file) {
        $results = [
            'upload' => null,
            'transcription' => null,
            'translation' => null,
            'sentiment_analysis' => null,
            'conversation_summary' => null,
            'errors' => []
        ];
        
        try {
            // Step 1: Upload audio
            $this->log("Starting audio analysis pipeline");
            $uploadResult = $this->uploadAudio($file);
            $results['upload'] = $uploadResult;
            
            if (!$uploadResult['success']) {
                $results['errors'][] = "Upload failed: " . $uploadResult['error'];
                return $results;
            }
            
            // Step 2: Transcribe audio
            $transcriptionResult = $this->transcribeAudio(
                $uploadResult['filepath'], 
                $uploadResult['mime_type']
            );
            $results['transcription'] = $transcriptionResult;
            
            if (!$transcriptionResult['success']) {
                $results['errors'][] = "Transcription failed: " . $transcriptionResult['error'];
                return $results;
            }
            
            // Step 3: Translate to English
            $translationResult = $this->translateToEnglish($transcriptionResult['transcription']);
            $results['translation'] = $translationResult;
            
            if (!$translationResult['success']) {
                $results['errors'][] = "Translation failed: " . $translationResult['error'];
                return $results;
            }
            
            // Step 4: Sentiment Analysis
            $sentimentResult = $this->analyzeSentiment(
                $transcriptionResult['transcription'],
                $translationResult['translation']
            );
            $results['sentiment_analysis'] = $sentimentResult;
            
            // Step 5: Generate Conversation Summary
            $summaryResult = $this->generateConversationSummary(
                $transcriptionResult['transcription'],
                $translationResult['translation'],
                $sentimentResult['analysis']
            );
            $results['conversation_summary'] = $summaryResult;
            
            if (!$summaryResult['success']) {
                $results['errors'][] = "Conversation summary failed: " . $summaryResult['error'];
                return $results;
            }
            
            $this->log("Audio analysis pipeline completed successfully");
            
            // Clean up uploaded file
            if (file_exists($uploadResult['filepath'])) {
                unlink($uploadResult['filepath']);
                $this->log("Temporary file cleaned up: " . $uploadResult['filename']);
            }
            
        } catch (Exception $e) {
            $this->log("Pipeline error: " . $e->getMessage(), 'ERROR');
            $results['errors'][] = "Pipeline error: " . $e->getMessage();
        }
        
    /**
     * Generate overall conversation summary
     */
    public function generateConversationSummary($originalTranscription, $englishTranslation, $sentimentAnalysis) {
        try {
            $prompt = "Please provide a comprehensive conversation summary based on the following information:

ORIGINAL TRANSCRIPTION:
$originalTranscription

ENGLISH TRANSLATION:
$englishTranslation

SENTIMENT ANALYSIS:
" . json_encode($sentimentAnalysis, JSON_PRETTY_PRINT) . "

Please provide a detailed summary that includes:

1. **Main Topic/Subject**: What was the primary subject of the conversation?
2. **Key Points**: What were the main points discussed?
3. **Participants**: How many speakers were involved and their general roles?
4. **Duration**: Estimate the conversation length based on timestamps
5. **Outcomes**: What were the main conclusions or outcomes?
6. **Notable Insights**: Any important insights, decisions, or action items
7. **Overall Assessment**: Brief assessment of the conversation quality and effectiveness

Format the response as a structured summary with clear headings. Keep it concise but comprehensive.";

            $result = $this->makeGeminiRequest($prompt);

            $this->log("Conversation summary generated");
            return [
                'success' => true,
                'summary' => $result
            ];

        } catch (Exception $e) {
            $this->log("Conversation summary error: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}  
 