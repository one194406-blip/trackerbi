<?php
require_once 'config.php';
require_once 'FilenameParser.php';

/**
 * Database Manager for Audio Analysis Results
 */
class DatabaseManager {
    private $connection;
    private $connectionAttempts = 0;
    private $maxConnectionAttempts = 3;
    
    public function __construct() {
        $this->connect();
    }
    
    /**
     * Establish database connection with InfinityFree optimizations
     */
    private function connect() {
        $this->connectionAttempts++;
        
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USERNAME,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false, // Disable persistent connections for InfinityFree
                    PDO::ATTR_TIMEOUT => 30, // Set connection timeout
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='', NAMES utf8mb4 COLLATE utf8mb4_unicode_ci", // UTF-8 support
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true // Use buffered queries
                ]
            );
            
            // Set session variables for InfinityFree compatibility and UTF-8 support
            $this->connection->exec("SET SESSION wait_timeout=300");
            $this->connection->exec("SET SESSION interactive_timeout=300");
            $this->connection->exec("SET SESSION character_set_client=utf8mb4");
            $this->connection->exec("SET SESSION character_set_connection=utf8mb4");
            $this->connection->exec("SET SESSION character_set_results=utf8mb4");
            
        } catch (PDOException $e) {
            error_log("Database connection failed (attempt {$this->connectionAttempts}): " . $e->getMessage());
            
            if ($this->connectionAttempts < $this->maxConnectionAttempts) {
                sleep(1); // Wait 1 second before retry
                return $this->connect();
            }
            
            throw new Exception("Database connection failed after {$this->maxConnectionAttempts} attempts");
        }
    }
    
    /**
     * Check if connection is alive and reconnect if needed
     */
    private function ensureConnection() {
        try {
            // Test connection with a simple query
            $this->connection->query('SELECT 1');
        } catch (PDOException $e) {
            error_log("Connection lost, attempting to reconnect: " . $e->getMessage());
            $this->connectionAttempts = 0; // Reset attempt counter
            $this->connect();
        }
    }
    
    /**
     * Store audio analysis results in database
     */
    public function storeAnalysisResults($results, $filename, $fileSize, $originalFilename = null) {
        try {
            // Ensure connection is alive before proceeding
            $this->ensureConnection();
            
            $sessionId = uniqid('audio_', true);
            
            // Parse filename for structured data
            $filenameToparse = $originalFilename ?? $filename;
            $parsedFilename = FilenameParser::parseFilename($filenameToparse);
            error_log("DatabaseManager: Filename parsing result: " . json_encode($parsedFilename));
            
            // Extract data from results with proper UTF-8 encoding
            $transcription = null;
            $translation = null;
            $conversationSummary = null;
            
            if ($results['transcription']['success']) {
                $rawTranscription = $results['transcription']['transcription'];
                // Ensure proper UTF-8 encoding and handle long text
                $transcription = mb_convert_encoding($rawTranscription, 'UTF-8', 'auto');
                // Log transcription length for debugging
                error_log("Transcription length: " . strlen($transcription) . " characters");
            }
            
            if ($results['translation']['success']) {
                $rawTranslation = $results['translation']['translation'];
                $translation = mb_convert_encoding($rawTranslation, 'UTF-8', 'auto');
                error_log("Translation length: " . strlen($translation) . " characters");
            }
            
            if ($results['conversation_summary']['success']) {
                $rawSummary = $results['conversation_summary']['summary'];
                $conversationSummary = mb_convert_encoding($rawSummary, 'UTF-8', 'auto');
            }
            
            // Extract sentiment analysis data
            $sentimentData = $results['sentiment_analysis']['success'] ? $results['sentiment_analysis']['analysis'] : null;
            $sentimentScore = $sentimentData ? $sentimentData['sentiment_score']['numerical_score'] : 0;
            $sentimentConfidence = $sentimentData ? $sentimentData['sentiment_score']['confidence'] : 0.0;
            $primarySentiment = $sentimentData ? $sentimentData['overall_sentiment']['primary_sentiment'] : 'neutral';
            
            // Extract overall sentiment details
            $emotionalTone = $sentimentData && isset($sentimentData['overall_sentiment']['emotional_tone']) ? $sentimentData['overall_sentiment']['emotional_tone'] : null;
            $empathyLevel = $sentimentData && isset($sentimentData['overall_sentiment']['empathy_level']) ? $sentimentData['overall_sentiment']['empathy_level'] : 'medium';
            $politenessLevel = $sentimentData && isset($sentimentData['overall_sentiment']['politeness_level']) ? $sentimentData['overall_sentiment']['politeness_level'] : 'medium';
            
            // Extract frustration and positive indicators
            $frustrationIndicators = $sentimentData && isset($sentimentData['overall_sentiment']['frustration_indicators']) ? json_encode($sentimentData['overall_sentiment']['frustration_indicators']) : '[]';
            $positiveIndicators = $sentimentData && isset($sentimentData['overall_sentiment']['positive_indicators']) ? json_encode($sentimentData['overall_sentiment']['positive_indicators']) : '[]';
            
            // Extract agent performance scores
            $agentPerformance = $sentimentData ? $sentimentData['agent_performance'] : null;
            $clarityScore = $agentPerformance ? $agentPerformance['clarity_score'] : 0;
            $empathyScore = $agentPerformance ? $agentPerformance['empathy_score'] : 0;
            $professionalismScore = $agentPerformance ? $agentPerformance['professionalism_score'] : 0;
            $callOpeningScore = $agentPerformance && isset($agentPerformance['call_opening_score']) ? $agentPerformance['call_opening_score'] : 0;
            $callQualityScore = $agentPerformance && isset($agentPerformance['call_quality_score']) ? $agentPerformance['call_quality_score'] : 0;
            $callClosingScore = $agentPerformance && isset($agentPerformance['call_closing_score']) ? $agentPerformance['call_closing_score'] : 0;
            $overallPerformance = $agentPerformance ? $agentPerformance['overall_performance'] : 'good';
            
            // Extract call structure analysis
            $openingAssessment = $agentPerformance && isset($agentPerformance['call_structure_analysis']['opening_assessment']) ? $agentPerformance['call_structure_analysis']['opening_assessment'] : null;
            $qualityAssessment = $agentPerformance && isset($agentPerformance['call_structure_analysis']['quality_assessment']) ? $agentPerformance['call_structure_analysis']['quality_assessment'] : null;
            $closingAssessment = $agentPerformance && isset($agentPerformance['call_structure_analysis']['closing_assessment']) ? $agentPerformance['call_structure_analysis']['closing_assessment'] : null;
            
            // Extract agent performance details
            $strengths = $agentPerformance && isset($agentPerformance['strengths']) ? json_encode($agentPerformance['strengths']) : '[]';
            $areasForImprovement = $agentPerformance && isset($agentPerformance['areas_for_improvement']) ? json_encode($agentPerformance['areas_for_improvement']) : '[]';
            $recommendations = $agentPerformance && isset($agentPerformance['recommendations']) ? json_encode($agentPerformance['recommendations']) : '[]';
            
            // Prepare JSON data for complex structures
            $speakerAnalysisJson = $sentimentData && isset($sentimentData['speaker_analysis']) ? json_encode($sentimentData['speaker_analysis']) : null;
            $overallSentimentJson = $sentimentData && isset($sentimentData['overall_sentiment']) ? json_encode($sentimentData['overall_sentiment']) : null;
            $agentPerformanceJson = $agentPerformance ? json_encode($agentPerformance) : null;
            $callStructureJson = $agentPerformance && isset($agentPerformance['call_structure_analysis']) ? json_encode($agentPerformance['call_structure_analysis']) : null;
            
            // Determine processing status
            $processingStatus = 'completed';
            $errorMessages = '';
            if (!empty($results['errors'])) {
                $processingStatus = 'failed';
                $errorMessages = implode('; ', $results['errors']);
            }
            
            $sql = "INSERT INTO audio_analysis_results (
                session_id, filename, file_size, upload_timestamp, created_at, updated_at,
                original_transcription, english_translation, conversation_summary,
                sentiment_score, sentiment_confidence, primary_sentiment, emotional_tone,
                empathy_level, politeness_level,
                clarity_score, empathy_score, professionalism_score,
                call_opening_score, call_quality_score, call_closing_score, overall_performance,
                opening_assessment, quality_assessment, closing_assessment,
                strengths, areas_for_improvement, recommendations,
                frustration_indicators, positive_indicators,
                speaker_analysis, overall_sentiment_data, agent_performance_data, call_structure_analysis,
                processing_status, error_messages,
                phone_number, call_language, caller_name, call_date, call_time, original_filename, filename_parsed
            ) VALUES (
                :session_id, :filename, :file_size, NOW(), NOW(), NOW(),
                :original_transcription, :english_translation, :conversation_summary,
                :sentiment_score, :sentiment_confidence, :primary_sentiment, :emotional_tone,
                :empathy_level, :politeness_level,
                :clarity_score, :empathy_score, :professionalism_score,
                :call_opening_score, :call_quality_score, :call_closing_score, :overall_performance,
                :opening_assessment, :quality_assessment, :closing_assessment,
                :strengths, :areas_for_improvement, :recommendations,
                :frustration_indicators, :positive_indicators,
                :speaker_analysis, :overall_sentiment_data, :agent_performance_data, :call_structure_analysis,
                :processing_status, :error_messages,
                :phone_number, :call_language, :caller_name, :call_date, :call_time, :original_filename, :filename_parsed
            )";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':session_id' => $sessionId,
                ':filename' => $filename,
                ':file_size' => $fileSize,
                ':original_transcription' => $transcription,
                ':english_translation' => $translation,
                ':conversation_summary' => $conversationSummary,
                ':sentiment_score' => $sentimentScore,
                ':sentiment_confidence' => $sentimentConfidence,
                ':primary_sentiment' => $primarySentiment,
                ':emotional_tone' => $emotionalTone,
                ':empathy_level' => $empathyLevel,
                ':politeness_level' => $politenessLevel,
                ':clarity_score' => $clarityScore,
                ':empathy_score' => $empathyScore,
                ':professionalism_score' => $professionalismScore,
                ':call_opening_score' => $callOpeningScore,
                ':call_quality_score' => $callQualityScore,
                ':call_closing_score' => $callClosingScore,
                ':overall_performance' => $overallPerformance,
                ':opening_assessment' => $openingAssessment,
                ':quality_assessment' => $qualityAssessment,
                ':closing_assessment' => $closingAssessment,
                ':strengths' => $strengths,
                ':areas_for_improvement' => $areasForImprovement,
                ':recommendations' => $recommendations,
                ':frustration_indicators' => $frustrationIndicators,
                ':positive_indicators' => $positiveIndicators,
                ':speaker_analysis' => $speakerAnalysisJson,
                ':overall_sentiment_data' => $overallSentimentJson,
                ':agent_performance_data' => $agentPerformanceJson,
                ':call_structure_analysis' => $callStructureJson,
                ':processing_status' => $processingStatus,
                ':error_messages' => $errorMessages,
                ':phone_number' => $parsedFilename['phone_number'],
                ':call_language' => $parsedFilename['language'],
                ':caller_name' => $parsedFilename['caller_name'],
                ':call_date' => $parsedFilename['call_date'],
                ':call_time' => $parsedFilename['call_time'],
                ':original_filename' => $parsedFilename['original_filename'],
                ':filename_parsed' => $parsedFilename['success'] ? 1 : 0
            ]);
            
            return [
                'success' => true,
                'session_id' => $sessionId,
                'id' => $this->connection->lastInsertId()
            ];
            
        } catch (PDOException $e) {
            // Handle "MySQL server has gone away" specifically
            if (strpos($e->getMessage(), 'MySQL server has gone away') !== false || 
                strpos($e->getMessage(), 'Lost connection') !== false) {
                
                error_log("MySQL connection lost during storage, attempting reconnect: " . $e->getMessage());
                
                try {
                    // Force reconnection
                    $this->connectionAttempts = 0;
                    $this->connect();
                    
                    // Retry the operation once
                    $stmt = $this->connection->prepare($sql);
                    $stmt->execute([
                        ':session_id' => $sessionId,
                        ':filename' => $filename,
                        ':file_size' => $fileSize,
                        ':original_transcription' => $transcription,
                        ':english_translation' => $translation,
                        ':conversation_summary' => $conversationSummary,
                        ':sentiment_score' => $sentimentScore,
                        ':sentiment_confidence' => $sentimentConfidence,
                        ':primary_sentiment' => $primarySentiment,
                        ':emotional_tone' => $emotionalTone,
                        ':empathy_level' => $empathyLevel,
                        ':politeness_level' => $politenessLevel,
                        ':clarity_score' => $clarityScore,
                        ':empathy_score' => $empathyScore,
                        ':professionalism_score' => $professionalismScore,
                        ':call_opening_score' => $callOpeningScore,
                        ':call_quality_score' => $callQualityScore,
                        ':call_closing_score' => $callClosingScore,
                        ':overall_performance' => $overallPerformance,
                        ':opening_assessment' => $openingAssessment,
                        ':quality_assessment' => $qualityAssessment,
                        ':closing_assessment' => $closingAssessment,
                        ':strengths' => $strengths,
                        ':areas_for_improvement' => $areasForImprovement,
                        ':recommendations' => $recommendations,
                        ':frustration_indicators' => $frustrationIndicators,
                        ':positive_indicators' => $positiveIndicators,
                        ':speaker_analysis' => $speakerAnalysisJson,
                        ':overall_sentiment_data' => $overallSentimentJson,
                        ':agent_performance_data' => $agentPerformanceJson,
                        ':call_structure_analysis' => $callStructureJson,
                        ':processing_status' => $processingStatus,
                        ':error_messages' => $errorMessages
                    ]);
                    
                    return [
                        'success' => true,
                        'session_id' => $sessionId,
                        'id' => $this->connection->lastInsertId(),
                        'reconnected' => true
                    ];
                    
                } catch (Exception $retryException) {
                    error_log("Database storage failed even after reconnection: " . $retryException->getMessage());
                    return [
                        'success' => false,
                        'error' => 'Database connection lost and reconnection failed: ' . $retryException->getMessage()
                    ];
                }
            }
            
            error_log("Database storage error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("Database storage error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get all analysis results for dashboard
     */
    public function getAllResults($limit = 50, $offset = 0) {
        try {
            $this->ensureConnection();
            $sql = "SELECT * FROM audio_analysis_results 
                    ORDER BY 
                        upload_timestamp DESC,
                        id DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Database retrieval error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get analytics summary data
     */
    public function getAnalyticsSummary() {
        try {
            $this->ensureConnection();
            $sql = "SELECT 
                        COUNT(*) as total_analyses,
                        AVG(sentiment_score) as avg_sentiment_score,
                        AVG(clarity_score) as avg_clarity_score,
                        AVG(empathy_score) as avg_empathy_score,
                        AVG(professionalism_score) as avg_professionalism_score,
                        AVG(call_opening_score) as avg_call_opening_score,
                        AVG(call_quality_score) as avg_call_quality_score,
                        AVG(call_closing_score) as avg_call_closing_score,
                        COUNT(CASE WHEN primary_sentiment = 'positive' THEN 1 END) as positive_count,
                        COUNT(CASE WHEN primary_sentiment = 'neutral' THEN 1 END) as neutral_count,
                        COUNT(CASE WHEN primary_sentiment = 'negative' THEN 1 END) as negative_count,
                        COUNT(CASE WHEN overall_performance = 'excellent' THEN 1 END) as excellent_count,
                        COUNT(CASE WHEN overall_performance = 'poor' THEN 1 END) as poor_count,
                        COUNT(CASE WHEN overall_performance = 'good' THEN 1 END) as good_count,
                        COUNT(CASE WHEN overall_performance = 'needs_improvement' THEN 1 END) as needs_improvement_count
                    FROM audio_analysis_results 
                    WHERE processing_status = 'completed'";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Analytics summary error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get recent analysis results
     */
    public function getRecentResults($limit = 10) {
        try {
            $this->ensureConnection();
            $sql = "SELECT session_id, filename, upload_timestamp, sentiment_score, 
                           clarity_score, empathy_score, professionalism_score,
                           call_opening_score, call_quality_score, call_closing_score,
                           primary_sentiment, overall_performance,
                           phone_number, call_language, caller_name, call_date, call_time, 
                           original_filename, filename_parsed
                    FROM audio_analysis_results 
                    WHERE processing_status = 'completed'
                    ORDER BY 
                        upload_timestamp DESC,
                        id DESC
                    LIMIT :limit";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Recent results error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get performance trends over time with full details
     */
    public function getPerformanceTrends($days = 30) {
        try {
            $this->ensureConnection();
            // First, let's try a simpler query to see if the basic data works
            $sql = "SELECT 
                        DATE(upload_timestamp) as analysis_date,
                        AVG(sentiment_score) as avg_sentiment,
                        AVG(clarity_score) as avg_clarity,
                        AVG(empathy_score) as avg_empathy,
                        AVG(professionalism_score) as avg_professionalism,
                        AVG(call_opening_score) as avg_opening,
                        AVG(call_quality_score) as avg_quality,
                        AVG(call_closing_score) as avg_closing,
                        COUNT(*) as daily_count
                    FROM audio_analysis_results 
                    WHERE processing_status = 'completed' 
                    AND upload_timestamp IS NOT NULL
                    AND upload_timestamp >= DATE_SUB(NOW(), INTERVAL :days DAY)
                    GROUP BY DATE(upload_timestamp)
                    ORDER BY analysis_date DESC";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll();
            
            // Now get detailed data for each date
            foreach ($results as &$result) {
                $detailSql = "SELECT 
                                id, session_id, filename, 
                                original_transcription, english_translation, conversation_summary,
                                sentiment_score, clarity_score, empathy_score, professionalism_score,
                                call_opening_score, call_quality_score, call_closing_score,
                                primary_sentiment, overall_performance, upload_timestamp,
                                phone_number, call_language, caller_name, call_date, call_time, 
                                original_filename, filename_parsed
                            FROM audio_analysis_results 
                            WHERE DATE(upload_timestamp) = :analysis_date
                            AND processing_status = 'completed'
                            ORDER BY 
                                upload_timestamp DESC,
                                id DESC";
                
                $detailStmt = $this->connection->prepare($detailSql);
                $detailStmt->bindValue(':analysis_date', $result['analysis_date']);
                $detailStmt->execute();
                
                $result['parsed_details'] = $detailStmt->fetchAll();
            }
            
            return $results;
        } catch (Exception $e) {
            error_log("Performance trends error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get today's performance data
     */
    public function getTodayPerformance() {
        try {
            $this->ensureConnection();
            $sql = "SELECT 
                        COUNT(*) as total_analyses_today,
                        AVG(sentiment_score) as avg_sentiment_today,
                        AVG(clarity_score) as avg_clarity_today,
                        AVG(empathy_score) as avg_empathy_today,
                        AVG(professionalism_score) as avg_professionalism_today,
                        AVG(call_opening_score) as avg_opening_today,
                        AVG(call_quality_score) as avg_quality_today,
                        AVG(call_closing_score) as avg_closing_today,
                        COUNT(CASE WHEN primary_sentiment = 'positive' THEN 1 END) as positive_today,
                        COUNT(CASE WHEN primary_sentiment = 'neutral' THEN 1 END) as neutral_today,
                        COUNT(CASE WHEN primary_sentiment = 'negative' THEN 1 END) as negative_today,
                        COUNT(CASE WHEN overall_performance = 'excellent' THEN 1 END) as excellent_today,
                        COUNT(CASE WHEN overall_performance = 'good' THEN 1 END) as good_today,
                        COUNT(CASE WHEN overall_performance = 'needs_improvement' THEN 1 END) as needs_improvement_today
                    FROM audio_analysis_results 
                    WHERE processing_status = 'completed' 
                    AND DATE(upload_timestamp) = CURDATE()";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Today performance error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get hourly performance trends for today
     */
    public function getTodayHourlyTrends() {
        try {
            $this->ensureConnection();
            $sql = "SELECT 
                        HOUR(upload_timestamp) as hour_of_day,
                        AVG(sentiment_score) as avg_sentiment,
                        AVG(clarity_score) as avg_clarity,
                        AVG(empathy_score) as avg_empathy,
                        AVG(professionalism_score) as avg_professionalism,
                        AVG(call_opening_score) as avg_opening,
                        AVG(call_quality_score) as avg_quality,
                        AVG(call_closing_score) as avg_closing,
                        COUNT(*) as hourly_count
                    FROM audio_analysis_results 
                    WHERE processing_status = 'completed' 
                    AND DATE(upload_timestamp) = CURDATE()
                    GROUP BY HOUR(upload_timestamp)
                    ORDER BY hour_of_day ASC";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Today hourly trends error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Test database connection and return status
     */
    public function testConnection() {
        try {
            $this->ensureConnection();
            
            // Test with a simple query
            $stmt = $this->connection->query('SELECT 1 as test');
            $result = $stmt->fetch();
            
            return [
                'success' => true,
                'message' => 'Database connection successful',
                'test_result' => $result['test'] == 1
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get simple data count for debugging
     */
    public function getDataCount() {
        try {
            $this->ensureConnection();
            $sql = "SELECT 
                        COUNT(*) as total_records,
                        COUNT(CASE WHEN processing_status = 'completed' THEN 1 END) as completed_records,
                        COUNT(CASE WHEN upload_timestamp IS NOT NULL THEN 1 END) as with_upload_timestamp,
                        COUNT(CASE WHEN created_at IS NOT NULL THEN 1 END) as with_created_at
                    FROM audio_analysis_results";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Data count error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get quick statistics for dashboard
     */
    public function getQuickStats() {
        try {
            $this->ensureConnection();
            
            $stats = [];
            
            // Total analyses
            $stmt = $this->connection->prepare("SELECT COUNT(*) as total FROM audio_analysis_results");
            $stmt->execute();
            $stats['total_analyses'] = $stmt->fetch()['total'];
            
            // Today's analyses
            $stmt = $this->connection->prepare("SELECT COUNT(*) as today FROM audio_analysis_results WHERE DATE(upload_timestamp) = CURDATE()");
            $stmt->execute();
            $stats['today_analyses'] = $stmt->fetch()['today'];
            
            // Average sentiment score
            $stmt = $this->connection->prepare("SELECT AVG(sentiment_score) as avg_sentiment FROM audio_analysis_results WHERE sentiment_score IS NOT NULL");
            $stmt->execute();
            $result = $stmt->fetch();
            $stats['avg_sentiment'] = $result['avg_sentiment'] ? round($result['avg_sentiment'], 1) : 0;
            
            // Last analysis time
            $stmt = $this->connection->prepare("SELECT upload_timestamp FROM audio_analysis_results ORDER BY upload_timestamp DESC LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result && $result['upload_timestamp']) {
                $stats['last_analysis'] = date('H:i', strtotime($result['upload_timestamp']));
            } else {
                $stats['last_analysis'] = 'N/A';
            }
            
            return $stats;
        } catch (Exception $e) {
            error_log("Quick stats error: " . $e->getMessage());
            return [
                'total_analyses' => 0,
                'today_analyses' => 0,
                'avg_sentiment' => 0,
                'last_analysis' => 'N/A'
            ];
        }
    }

    /**
     * Get today's detailed analysis results
     */
    public function getTodayDetailedResults() {
        try {
            $this->ensureConnection();
            $sql = "SELECT 
                        id, session_id, filename, upload_timestamp,
                        original_transcription, english_translation, conversation_summary,
                        sentiment_score, clarity_score, empathy_score, professionalism_score,
                        call_opening_score, call_quality_score, call_closing_score,
                        primary_sentiment, overall_performance
                    FROM audio_analysis_results 
                    WHERE processing_status = 'completed' 
                    AND DATE(upload_timestamp) = CURDATE()
                    ORDER BY 
                        upload_timestamp DESC,
                        id DESC";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Today detailed results error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get connection info for debugging
     */
    public function getConnectionInfo() {
        try {
            $info = [
                'host' => DB_HOST,
                'database' => DB_NAME,
                'username' => DB_USERNAME,
                'connection_attempts' => $this->connectionAttempts,
                'max_attempts' => $this->maxConnectionAttempts
            ];
            
            if ($this->connection) {
                $info['server_info'] = $this->connection->getAttribute(PDO::ATTR_SERVER_INFO);
                $info['server_version'] = $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
                $info['connection_status'] = $this->connection->getAttribute(PDO::ATTR_CONNECTION_STATUS);
            }
            
            return $info;
        } catch (Exception $e) {
            return [
                'error' => 'Could not get connection info: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get today's analysis results with filename parsing
     */
    public function getTodayResults() {
        try {
            $this->ensureConnection();
            $sql = "SELECT 
                        id, session_id, filename, upload_timestamp, sentiment_score, 
                        clarity_score, empathy_score, professionalism_score,
                        call_opening_score, call_quality_score, call_closing_score,
                        primary_sentiment, overall_performance,
                        phone_number, call_language, caller_name, call_date, call_time, 
                        original_filename, filename_parsed,
                        original_transcription, english_translation, conversation_summary
                    FROM audio_analysis_results 
                    WHERE DATE(upload_timestamp) = CURDATE()
                    AND processing_status = 'completed'
                    ORDER BY upload_timestamp DESC";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Today results error: " . $e->getMessage());
            return [];
        }
    }
}
