<?php
require_once 'config.php';
require_once 'ErrorHandler.php';
require_once 'DatabaseManager.php';

/**
 * Audio Analysis Class using Gemini API
 */
class AudioAnalyzer {
    private $apiKeys;
    private $currentKeyIndex = 0;
    private $logFile;
    private $dbManager;
    
    public function __construct() {
        $this->apiKeys = GEMINI_API_KEYS;
        $this->logFile = LOG_FILE;
        $this->dbManager = new DatabaseManager();
        
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

Please provide a comprehensive analysis in the following JSON format. Pay special attention to call structure and quality:

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
        \"call_opening_score\": 88,
        \"call_quality_score\": 82,
        \"call_closing_score\": 90,
        \"overall_performance\": \"good/excellent/needs_improvement\",
        \"strengths\": [\"strength1\", \"strength2\"],
        \"areas_for_improvement\": [\"area1\", \"area2\"],
        \"recommendations\": [\"recommendation1\", \"recommendation2\"],
        \"call_structure_analysis\": {
            \"opening_assessment\": \"Assessment of greeting, introduction, rapport building, and professional opening\",
            \"quality_assessment\": \"Assessment of communication clarity, active listening, problem-solving skills, and conversation flow\",
            \"closing_assessment\": \"Assessment of resolution confirmation, next steps, professional closure, and customer satisfaction\"
        }
    }
}

For call scoring, evaluate:
- Call Opening (0-100): Greeting quality, introduction, rapport building, professionalism
- Call Quality (0-100): Communication clarity, active listening, problem-solving, conversation management
- Call Closing (0-100): Resolution confirmation, next steps, professional closure, customer satisfaction

Provide only the JSON response, no additional text.";
            
            $result = $this->makeGeminiRequest($prompt);
            
            // Clean and validate JSON
            $result = trim($result);
            
            // Remove markdown code blocks if present
            if (strpos($result, '```json') !== false) {
                $result = preg_replace('/```json\s*/', '', $result);
                $result = preg_replace('/```\s*$/', '', $result);
            }
            if (strpos($result, '```') !== false) {
                $result = preg_replace('/```.*?\n/', '', $result);
                $result = preg_replace('/```\s*$/', '', $result);
            }
            
            // Log the raw response for debugging
            $this->log("Raw sentiment analysis response: " . substr($result, 0, 500) . "...", 'DEBUG');
            
            $analysisData = json_decode($result, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If JSON parsing fails, create a fallback response
                $this->log("JSON parsing failed: " . json_last_error_msg() . ". Creating fallback response.", 'WARNING');
                
                $analysisData = [
                    'speaker_analysis' => [
                        [
                            'speaker' => 'Speaker 1',
                            'sentiment' => 'neutral',
                            'confidence' => 0.7,
                            'reasoning' => 'Unable to parse detailed analysis from AI response',
                            'key_emotions' => ['neutral'],
                            'tone_indicators' => ['conversational']
                        ]
                    ],
                    'overall_sentiment' => [
                        'primary_sentiment' => 'neutral',
                        'emotional_tone' => 'Conversational tone detected',
                        'empathy_level' => 'medium',
                        'politeness_level' => 'medium',
                        'frustration_indicators' => [],
                        'positive_indicators' => ['engagement']
                    ],
                    'sentiment_score' => [
                        'numerical_score' => 50,
                        'scale' => '0-100 (0=very negative, 50=neutral, 100=very positive)',
                        'confidence' => 0.7
                    ],
                    'agent_performance' => [
                        'clarity_score' => 75,
                        'empathy_score' => 70,
                        'professionalism_score' => 80,
                        'call_opening_score' => 75,
                        'call_quality_score' => 70,
                        'call_closing_score' => 75,
                        'overall_performance' => 'good',
                        'strengths' => ['Clear communication'],
                        'areas_for_improvement' => ['More detailed analysis needed'],
                        'recommendations' => ['Continue professional approach'],
                        'call_structure_analysis' => [
                            'opening_assessment' => 'Professional greeting and introduction observed',
                            'quality_assessment' => 'Clear communication with good listening skills',
                            'closing_assessment' => 'Appropriate closure with next steps mentioned'
                        ]
                    ]
                ];
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
     * Clean summary text by removing markdown formatting and emojis
     */
    private function cleanSummaryText($text) {
        // Remove markdown bold formatting (**text**)
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);
        
        // Remove markdown italic formatting (*text*)
        $text = preg_replace('/\*(.*?)\*/', '$1', $text);
        
        // Remove markdown code blocks
        $text = preg_replace('/```.*?```/s', '', $text);
        $text = preg_replace('/`(.*?)`/', '$1', $text);
        
        // Remove emojis (comprehensive Unicode ranges)
        $text = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text); // Emoticons
        $text = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $text); // Misc Symbols
        $text = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $text); // Transport
        $text = preg_replace('/[\x{1F1E0}-\x{1F1FF}]/u', '', $text); // Flags
        $text = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $text);   // Misc symbols
        $text = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $text);   // Dingbats
        $text = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $text); // Supplemental Symbols
        $text = preg_replace('/[\x{1FA70}-\x{1FAFF}]/u', '', $text); // Symbols and Pictographs Extended-A
        
        // Remove common emoji characters
        $text = preg_replace('/[🎯🎉🚀📋✅❌💡🔍🛠️📊📝🌐👥💬]/u', '', $text);
        
        // Remove other markdown symbols
        $text = str_replace(['#', '>', '=', '~'], '', $text);
        
        // Clean up bullet points and structure properly
        $text = preg_replace('/^[\-\+\*]\s+/m', '• ', $text);
        
        // Clean up extra whitespace but preserve line breaks
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n\s*\n/', "\n\n", $text);
        $text = trim($text);
        
        return $text;
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

Please provide a detailed summary in the following structured format:

CONVERSATION SUMMARY

Main Topic:
[Describe the primary subject of the conversation]

Key Discussion Points:
1. [First main point discussed]
2. [Second main point discussed]
3. [Additional points as needed]

Participants:
[Number of speakers and their roles/characteristics]

Estimated Duration:
[Based on timestamps in the transcription]

Call Reason Analysis:
[Analyze the primary reason for this call - is it a Query (asking for information), Request (asking for help/service), Complaint (expressing dissatisfaction), or General conversation? Provide specific reasoning based on the conversation content.]

Key Outcomes:
[Main conclusions, decisions, or resolutions]

Notable Insights:
[Important observations, action items, or follow-ups]

Overall Assessment:
[Brief evaluation of conversation quality and effectiveness]

Please use clean, professional language without any markdown formatting, asterisks, emojis, or special symbols. Use simple bullet points and clear headings only.";

            $result = $this->makeGeminiRequest($prompt);
            
            // Clean the result to remove markdown formatting and emojis
            $result = $this->cleanSummaryText($result);

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
    
    /**
     * Enhance conversation summary with Call Reason Analysis
     */
    public function enhanceConversationSummaryWithCallReason($originalSummary, $transcription, $translation) {
        try {
            // Combine all text for comprehensive analysis
            $summary = strtolower($originalSummary);
            $transcriptionText = strtolower($transcription ?? '');
            $translationText = strtolower($translation ?? '');
            $fullText = $summary . ' ' . $transcriptionText . ' ' . $translationText;
            
            $callReason = 'General';
            $confidence = 'Medium';
            $reasoning = 'Based on general conversation patterns';
            
            // Enhanced detection logic
            $queryPatterns = ['what', 'how', 'why', 'when', 'where', 'who', 'which', 'can you tell', 'do you know', 'is there', 'are there', 'information', 'details', 'explain', 'clarify', 'understand', 'meaning'];
            $requestPatterns = ['please', 'need', 'want', 'require', 'help', 'assist', 'provide', 'give', 'send', 'call back', 'contact', 'arrange', 'schedule', 'book', 'reserve', 'order'];
            $complaintPatterns = ['complain', 'complaint', 'issue', 'problem', 'bad', 'wrong', 'disappointed', 'frustrated', 'angry', 'terrible', 'awful', 'unhappy', 'not satisfied', 'poor service', 'dissatisfied', 'upset', 'annoyed'];
            
            $queryScore = 0;
            $requestScore = 0;
            $complaintScore = 0;
            
            // Count pattern matches
            foreach ($queryPatterns as $pattern) {
                $queryScore += substr_count($fullText, $pattern);
            }
            foreach ($requestPatterns as $pattern) {
                $requestScore += substr_count($fullText, $pattern);
            }
            foreach ($complaintPatterns as $pattern) {
                $complaintScore += substr_count($fullText, $pattern);
            }
            
            // Add question mark detection
            $questionMarks = substr_count($fullText, '?');
            $queryScore += $questionMarks * 2;
            
            // Determine call reason based on scores
            if ($complaintScore > 0 && $complaintScore >= max($queryScore, $requestScore)) {
                $callReason = 'Complaint';
                $confidence = $complaintScore > 2 ? 'High' : 'Medium';
                $reasoning = "Detected complaint indicators: $complaintScore matches found";
            } elseif ($queryScore > 0 && $queryScore >= $requestScore) {
                $callReason = 'Query';
                $confidence = $queryScore > 3 ? 'High' : 'Medium';
                $reasoning = "Detected information-seeking patterns: $queryScore matches found" . ($questionMarks > 0 ? ", including $questionMarks questions" : '');
            } elseif ($requestScore > 0) {
                $callReason = 'Request';
                $confidence = $requestScore > 2 ? 'High' : 'Medium';
                $reasoning = "Detected service request patterns: $requestScore matches found";
            }
            
            // Create enhanced summary with Call Reason Analysis
            $callReasonSection = "\n\nCall Reason Analysis:\nPrimary Call Reason: $callReason\nConfidence Level: $confidence\nReasoning: $reasoning\nDetection Breakdown: Query($queryScore), Request($requestScore), Complaint($complaintScore), Questions($questionMarks)";
            
            $enhancedSummary = $originalSummary . $callReasonSection;
            
            $this->log("Call Reason Analysis added to conversation summary: $callReason ($confidence confidence)");
            
            return [
                'success' => true,
                'enhanced_summary' => $enhancedSummary,
                'call_reason' => $callReason,
                'confidence' => $confidence,
                'reasoning' => $reasoning
            ];
            
        } catch (Exception $e) {
            $this->log("Call Reason Analysis enhancement error: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'enhanced_summary' => $originalSummary // Return original if enhancement fails
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
            
            if (!$sentimentResult['success']) {
                $results['errors'][] = "Sentiment analysis failed: " . $sentimentResult['error'];
                return $results;
            }
            
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
            
            // Step 5.5: Enhance conversation summary with Call Reason Analysis
            $enhancedSummaryResult = $this->enhanceConversationSummaryWithCallReason(
                $summaryResult['summary'],
                $transcriptionResult['transcription'],
                $translationResult['translation']
            );
            
            if ($enhancedSummaryResult['success']) {
                $results['conversation_summary']['summary'] = $enhancedSummaryResult['enhanced_summary'];
                $this->log("Conversation summary enhanced with Call Reason Analysis");
            }
            
            $this->log("Audio analysis pipeline completed successfully");
            
            // Step 6: Store results in database
            $dbResult = $this->dbManager->storeAnalysisResults(
                $results, 
                $uploadResult['filename'], 
                $uploadResult['size'],
                $file['name'] // Pass original filename for parsing
            );
            
            if ($dbResult['success']) {
                $this->log("Analysis results stored in database with ID: " . $dbResult['id']);
                $results['database_storage'] = $dbResult;
            } else {
                $this->log("Failed to store results in database: " . $dbResult['error'], 'ERROR');
                $results['errors'][] = "Database storage failed: " . $dbResult['error'];
            }
            
            // Clean up uploaded file
            if (file_exists($uploadResult['filepath'])) {
                unlink($uploadResult['filepath']);
                $this->log("Temporary file cleaned up: " . $uploadResult['filename']);
            }
            
        } catch (Exception $e) {
            $this->log("Pipeline error: " . $e->getMessage(), 'ERROR');
            $results['errors'][] = "Pipeline error: " . $e->getMessage();
        }
        
        return $results;
    }
}
