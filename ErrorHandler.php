<?php
/**
 * Comprehensive Error Handler for Audio Analysis System
 */

class ErrorHandler {
    private static $instance = null;
    private $logFile;
    private $errorCounts = [];
    
    private function __construct() {
        $this->logFile = defined('LOG_FILE') ? LOG_FILE : __DIR__ . '/logs/audio_analysis.log';
        $this->setupErrorHandling();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function setupErrorHandling() {
        // Set custom error handler
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    public function handleError($severity, $message, $file, $line) {
        $errorType = $this->getErrorType($severity);
        $this->logError($errorType, $message, $file, $line);
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    public function handleException($exception) {
        $this->logError(
            'EXCEPTION',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
    }
    
    public function handleShutdown() {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $this->logError(
                'FATAL',
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }
    
    private function getErrorType($severity) {
        $errorTypes = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED'
        ];
        
        return $errorTypes[$severity] ?? 'UNKNOWN';
    }
    
    private function logError($type, $message, $file = '', $line = 0, $trace = '') {
        $timestamp = date('Y-m-d H:i:s');
        $errorId = uniqid();
        
        // Count errors by type
        if (!isset($this->errorCounts[$type])) {
            $this->errorCounts[$type] = 0;
        }
        $this->errorCounts[$type]++;
        
        $logEntry = [
            'timestamp' => $timestamp,
            'error_id' => $errorId,
            'type' => $type,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip_address' => $this->getClientIP(),
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown'
        ];
        
        if ($trace) {
            $logEntry['stack_trace'] = $trace;
        }
        
        // Format log message
        $logMessage = "[$timestamp] [$type] [ID:$errorId] $message";
        if ($file && $line) {
            $logMessage .= " in $file:$line";
        }
        $logMessage .= " | IP: " . $logEntry['ip_address'];
        $logMessage .= " | URI: " . $logEntry['request_uri'];
        
        if ($trace) {
            $logMessage .= "\nStack Trace:\n$trace";
        }
        
        $logMessage .= "\n" . str_repeat('-', 80) . "\n";
        
        // Write to log file
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
        
        // Also write structured data for potential analysis
        $this->writeStructuredLog($logEntry);
    }
    
    private function writeStructuredLog($logEntry) {
        $structuredLogFile = dirname($this->logFile) . '/structured_errors.json';
        
        $existingData = [];
        if (file_exists($structuredLogFile)) {
            $content = file_get_contents($structuredLogFile);
            $existingData = json_decode($content, true) ?? [];
        }
        
        $existingData[] = $logEntry;
        
        // Keep only last 1000 entries to prevent file from growing too large
        if (count($existingData) > 1000) {
            $existingData = array_slice($existingData, -1000);
        }
        
        file_put_contents($structuredLogFile, json_encode($existingData, JSON_PRETTY_PRINT), LOCK_EX);
    }
    
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                return trim($ips[0]);
            }
        }
        
        return 'Unknown';
    }
    
    public function getErrorCounts() {
        return $this->errorCounts;
    }
    
    public function logCustomError($message, $context = []) {
        $contextStr = empty($context) ? '' : ' | Context: ' . json_encode($context);
        $this->logError('CUSTOM', $message . $contextStr);
    }
    
    public function logApiUsage($endpoint, $success, $processingTime = null, $fileSize = null) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [API_USAGE] Endpoint: $endpoint | Success: " . ($success ? 'YES' : 'NO');
        
        if ($processingTime !== null) {
            $logMessage .= " | Processing Time: {$processingTime}s";
        }
        
        if ($fileSize !== null) {
            $logMessage .= " | File Size: " . number_format($fileSize / 1024, 2) . "KB";
        }
        
        $logMessage .= " | IP: " . $this->getClientIP() . "\n";
        
        $usageLogFile = dirname($this->logFile) . '/api_usage.log';
        file_put_contents($usageLogFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

// Note: ErrorHandler will be initialized when first called, not automatically
