-- Run this in phpMyAdmin or MySQL command line
USE trackerbi_audio;

-- Add filename parsing columns
ALTER TABLE audio_analysis_results 
ADD COLUMN IF NOT EXISTS phone_number VARCHAR(20) NULL COMMENT 'Extracted phone number from filename',
ADD COLUMN IF NOT EXISTS call_language VARCHAR(50) NULL COMMENT 'Extracted language from filename', 
ADD COLUMN IF NOT EXISTS caller_name VARCHAR(100) NULL COMMENT 'Extracted caller/agent name from filename',
ADD COLUMN IF NOT EXISTS call_date DATE NULL COMMENT 'Extracted call date from filename timestamp',
ADD COLUMN IF NOT EXISTS call_time TIME NULL COMMENT 'Extracted call time from filename timestamp',
ADD COLUMN IF NOT EXISTS original_filename VARCHAR(500) NULL COMMENT 'Original uploaded filename before processing',
ADD COLUMN IF NOT EXISTS filename_parsed BOOLEAN DEFAULT FALSE COMMENT 'Whether filename has been successfully parsed';

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_phone_number ON audio_analysis_results(phone_number);
CREATE INDEX IF NOT EXISTS idx_call_language ON audio_analysis_results(call_language);
CREATE INDEX IF NOT EXISTS idx_caller_name ON audio_analysis_results(caller_name);
CREATE INDEX IF NOT EXISTS idx_call_date ON audio_analysis_results(call_date);
CREATE INDEX IF NOT EXISTS idx_call_time ON audio_analysis_results(call_time);
CREATE INDEX IF NOT EXISTS idx_filename_parsed ON audio_analysis_results(filename_parsed);

-- Show the updated table structure
DESCRIBE audio_analysis_results;

SELECT 'Filename parsing columns added successfully!' as status;
