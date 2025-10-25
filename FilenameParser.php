<?php

/**
 * Filename Parser for TrackerBI Audio Analysis
 * Parses structured filenames like: 9080093260_English_Nisarga_20251022164012
 * Pattern: {phone_number}_{language}_{name}_{YYYYMMDDHHMMSS}
 */
class FilenameParser {
    
    /**
     * Parse structured filename into components
     * 
     * @param string $filename Original filename (with or without extension)
     * @return array Parsed components or null if parsing fails
     */
    public static function parseFilename($filename) {
        try {
            // Remove file extension if present
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            
            // Log the filename being parsed
            error_log("FilenameParser: Parsing filename: " . $nameWithoutExt);
            
            // Expected pattern: phone_language_name_timestamp
            // Example: 9080093260_English_Nisarga_20251022164012
            $parts = explode('_', $nameWithoutExt);
            
            // We need at least 4 parts: phone, language, name, timestamp
            if (count($parts) < 4) {
                error_log("FilenameParser: Insufficient parts in filename. Expected 4+, got " . count($parts));
                return self::createEmptyResult($filename, false, "Insufficient filename parts");
            }
            
            // Extract components
            $phoneNumber = $parts[0];
            $language = $parts[1]; 
            
            // Handle names that might contain underscores
            // Everything between language and timestamp is considered the name
            $timestampPart = end($parts); // Last part is timestamp
            $nameParts = array_slice($parts, 2, -1); // Everything between language and timestamp
            $name = implode('_', $nameParts);
            
            // Validate phone number (should be numeric and reasonable length)
            if (!preg_match('/^\d{10,15}$/', $phoneNumber)) {
                error_log("FilenameParser: Invalid phone number format: " . $phoneNumber);
                return self::createEmptyResult($filename, false, "Invalid phone number format");
            }
            
            // Validate timestamp (should be 14 digits: YYYYMMDDHHMMSS)
            if (!preg_match('/^\d{14}$/', $timestampPart)) {
                error_log("FilenameParser: Invalid timestamp format: " . $timestampPart);
                return self::createEmptyResult($filename, false, "Invalid timestamp format");
            }
            
            // Parse timestamp: YYYYMMDDHHMMSS
            $year = substr($timestampPart, 0, 4);
            $month = substr($timestampPart, 4, 2);
            $day = substr($timestampPart, 6, 2);
            $hour = substr($timestampPart, 8, 2);
            $minute = substr($timestampPart, 10, 2);
            $second = substr($timestampPart, 12, 2);
            
            // Validate date components
            if (!checkdate($month, $day, $year)) {
                error_log("FilenameParser: Invalid date: $year-$month-$day");
                return self::createEmptyResult($filename, false, "Invalid date in timestamp");
            }
            
            // Validate time components
            if ($hour > 23 || $minute > 59 || $second > 59) {
                error_log("FilenameParser: Invalid time: $hour:$minute:$second");
                return self::createEmptyResult($filename, false, "Invalid time in timestamp");
            }
            
            // Format date and time
            $callDate = "$year-$month-$day";
            $callTime = "$hour:$minute:$second";
            
            // Create result
            $result = [
                'success' => true,
                'original_filename' => $filename,
                'phone_number' => $phoneNumber,
                'language' => ucfirst(strtolower($language)), // Normalize language
                'caller_name' => $name,
                'call_date' => $callDate,
                'call_time' => $callTime,
                'timestamp_raw' => $timestampPart,
                'parsed_at' => date('Y-m-d H:i:s'),
                'error_message' => null
            ];
            
            error_log("FilenameParser: Successfully parsed - Phone: $phoneNumber, Language: $language, Name: $name, Date: $callDate, Time: $callTime");
            
            return $result;
            
        } catch (Exception $e) {
            error_log("FilenameParser: Exception during parsing: " . $e->getMessage());
            return self::createEmptyResult($filename, false, "Parsing exception: " . $e->getMessage());
        }
    }
    
    /**
     * Create empty result structure for failed parsing
     */
    private static function createEmptyResult($filename, $success = false, $errorMessage = null) {
        return [
            'success' => $success,
            'original_filename' => $filename,
            'phone_number' => null,
            'language' => null,
            'caller_name' => null,
            'call_date' => null,
            'call_time' => null,
            'timestamp_raw' => null,
            'parsed_at' => date('Y-m-d H:i:s'),
            'error_message' => $errorMessage
        ];
    }
    
    /**
     * Validate if filename follows expected pattern
     */
    public static function isValidPattern($filename) {
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        $parts = explode('_', $nameWithoutExt);
        
        // Check basic structure
        if (count($parts) < 4) {
            return false;
        }
        
        // Check phone number (first part)
        if (!preg_match('/^\d{10,15}$/', $parts[0])) {
            return false;
        }
        
        // Check timestamp (last part)
        $timestampPart = end($parts);
        if (!preg_match('/^\d{14}$/', $timestampPart)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get filename pattern description
     */
    public static function getPatternDescription() {
        return [
            'pattern' => '{phone_number}_{language}_{name}_{YYYYMMDDHHMMSS}',
            'example' => '9080093260_English_Nisarga_20251022164012',
            'components' => [
                'phone_number' => '10-15 digit phone number',
                'language' => 'Call language (English, Hindi, etc.)',
                'name' => 'Caller or agent name (can contain underscores)',
                'timestamp' => 'Date and time in YYYYMMDDHHMMSS format'
            ],
            'rules' => [
                'Phone number must be 10-15 digits',
                'Language can be any text',
                'Name can contain underscores and will be joined',
                'Timestamp must be exactly 14 digits',
                'Date must be valid (YYYY-MM-DD)',
                'Time must be valid (HH:MM:SS in 24-hour format)'
            ]
        ];
    }
    
    /**
     * Format parsed data for display
     */
    public static function formatForDisplay($parsedData) {
        if (!$parsedData['success']) {
            return [
                'display_name' => $parsedData['original_filename'],
                'details' => 'Filename parsing failed: ' . ($parsedData['error_message'] ?? 'Unknown error'),
                'structured' => false
            ];
        }
        
        $date = date('M j, Y', strtotime($parsedData['call_date']));
        $time = date('g:i A', strtotime($parsedData['call_time']));
        
        return [
            'display_name' => $parsedData['caller_name'] . ' (' . $parsedData['language'] . ')',
            'phone_number' => $parsedData['phone_number'],
            'language' => $parsedData['language'],
            'caller_name' => $parsedData['caller_name'],
            'call_datetime' => $date . ' at ' . $time,
            'call_date' => $date,
            'call_time' => $time,
            'details' => "📞 {$parsedData['phone_number']} | 🌐 {$parsedData['language']} | 👤 {$parsedData['caller_name']} | 📅 $date at $time",
            'structured' => true
        ];
    }
}
?>
