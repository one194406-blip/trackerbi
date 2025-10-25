-- TrackerBI Audio Analysis Database Setup
-- Run this SQL script to create the required database and tables
-- 
-- FILENAME PARSING FEATURE:
-- Supports structured filenames with pattern: {phone}_{language}_{name}_{YYYYMMDDHHMMSS}
-- Example: 9080093260_English_Nisarga_20251022164012.mp3
-- Automatically extracts: phone number, language, caller name, date, and time

CREATE DATABASE IF NOT EXISTS trackerbi_audio;
USE trackerbi_audio;

-- ============================================
-- TrackerBI Audio Analysis Table
-- ============================================
CREATE TABLE IF NOT EXISTS audio_analysis_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(50) UNIQUE NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    upload_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Filename Parsing Components (Pattern: phone_language_name_YYYYMMDDHHMMSS)
    phone_number VARCHAR(20) NULL COMMENT 'Extracted phone number from filename',
    call_language VARCHAR(50) NULL COMMENT 'Extracted language from filename', 
    caller_name VARCHAR(100) NULL COMMENT 'Extracted caller/agent name from filename',
    call_date DATE NULL COMMENT 'Extracted call date from filename timestamp',
    call_time TIME NULL COMMENT 'Extracted call time from filename timestamp',
    original_filename VARCHAR(500) NULL COMMENT 'Original uploaded filename before processing',
    filename_parsed BOOLEAN DEFAULT FALSE COMMENT 'Whether filename has been successfully parsed',
    
    -- Transcription Data
    original_transcription TEXT,
    english_translation TEXT,
    
    -- Sentiment Analysis Scores
    sentiment_score INT DEFAULT 0,
    sentiment_confidence DECIMAL(3,2) DEFAULT 0.00,
    primary_sentiment ENUM('positive', 'neutral', 'negative') DEFAULT 'neutral',
    emotional_tone TEXT,
    empathy_level ENUM('high', 'medium', 'low') DEFAULT 'medium',
    politeness_level ENUM('high', 'medium', 'low') DEFAULT 'medium',
    
    -- Agent Performance Scores
    clarity_score INT DEFAULT 0,
    empathy_score INT DEFAULT 0,
    professionalism_score INT DEFAULT 0,
    call_opening_score INT DEFAULT 0,
    call_quality_score INT DEFAULT 0,
    call_closing_score INT DEFAULT 0,
    overall_performance ENUM('excellent', 'good', 'needs_improvement') DEFAULT 'good',
    
    -- Call Structure Analysis Text
    opening_assessment TEXT,
    quality_assessment TEXT,
    closing_assessment TEXT,
    
    -- Agent Performance Details
    strengths TEXT, -- JSON array stored as text
    areas_for_improvement TEXT, -- JSON array stored as text
    recommendations TEXT, -- JSON array stored as text
    
    -- Sentiment Details
    frustration_indicators TEXT, -- JSON array stored as text
    positive_indicators TEXT, -- JSON array stored as text
    
    -- Analysis Details (JSON format for complex data)
    speaker_analysis JSON,
    overall_sentiment_data JSON,
    agent_performance_data JSON,
    call_structure_analysis JSON,
    conversation_summary TEXT,
    
    -- Processing Status
    processing_status ENUM('completed', 'failed', 'processing') DEFAULT 'completed',
    error_messages TEXT,
    
    -- Timestamps
    analysis_completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better performance
    INDEX idx_upload_timestamp (upload_timestamp),
    INDEX idx_sentiment_score (sentiment_score),
    INDEX idx_overall_performance (overall_performance),
    INDEX idx_processing_status (processing_status),
    INDEX idx_primary_sentiment (primary_sentiment),
    INDEX idx_empathy_level (empathy_level),
    INDEX idx_call_opening_score (call_opening_score),
    INDEX idx_call_quality_score (call_quality_score),
    INDEX idx_call_closing_score (call_closing_score),
    
    -- Filename parsing indexes
    INDEX idx_phone_number (phone_number),
    INDEX idx_call_language (call_language),
    INDEX idx_caller_name (caller_name),
    INDEX idx_call_date (call_date),
    INDEX idx_call_time (call_time),
    INDEX idx_filename_parsed (filename_parsed)
);

-- Insert sample data for TrackerBI testing
INSERT IGNORE INTO audio_analysis_results (
    session_id, filename, file_size, original_transcription, english_translation,
    sentiment_score, sentiment_confidence, primary_sentiment, emotional_tone,
    empathy_level, politeness_level,
    clarity_score, empathy_score, professionalism_score,
    call_opening_score, call_quality_score, call_closing_score,
    overall_performance, conversation_summary,
    opening_assessment, quality_assessment, closing_assessment,
    strengths, areas_for_improvement, recommendations,
    frustration_indicators, positive_indicators,
    phone_number, call_language, caller_name, call_date, call_time, original_filename, filename_parsed
) VALUES 
(
    'sample_001', 'sample_call.mp3', 1024000, 
    'Sample transcription in original language', 'Sample English translation',
    75, 0.85, 'positive', 'Professional and courteous tone throughout the conversation',
    'high', 'high',
    80, 75, 90, 85, 78, 88,
    'good', 'Sample conversation summary for testing purposes',
    'Professional greeting with clear introduction and rapport building',
    'Clear communication with active listening and effective problem-solving approach',
    'Appropriate closure with confirmation of resolution and next steps',
    '["Clear communication", "Professional demeanor", "Active listening"]',
    '["Could improve response time", "More detailed explanations needed"]',
    '["Continue professional approach", "Focus on quicker issue resolution"]',
    '["None identified"]',
    '["Professional tone", "Clear explanations", "Customer satisfaction"]',
    '9080093260', 'English', 'Sample_User', '2025-10-22', '16:40:12', '9080093260_English_Sample_User_20251022164012.mp3', TRUE
);

-- ============================================
-- ProductAuth Authentication System
-- ============================================
-- Create users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample admin user
-- Email: admin@example.com
-- Password: admin123
INSERT IGNORE INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@example.com', 'admin123', 'admin');

-- Insert sample regular user
-- Email: user@example.com
-- Password: user123
INSERT IGNORE INTO users (name, email, password, role) VALUES 
('John Doe', 'user@example.com', 'user123', 'user');

-- Insert additional sample users for testing
INSERT IGNORE INTO users (name, email, password, role) VALUES 
('Jane Smith', 'jane@example.com', 'user123', 'user'),
('Mike Johnson', 'mike@example.com', 'user123', 'user'),
('Sarah Wilson', 'sarah@example.com', 'admin123', 'admin');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_created_at ON users(created_at);

-- Verify the complete setup
SELECT 'Complete database setup completed successfully!' as status;
SELECT COUNT(*) as total_audio_analyses FROM audio_analysis_results;
SELECT COUNT(*) as total_users FROM users;
SELECT role, COUNT(*) as count FROM users GROUP BY role;

-- ============================================
-- Login Credentials for Testing:
-- ============================================
-- Admin Users:
--   - admin@example.com / admin123
--   - sarah@example.com / admin123
-- 
-- Regular Users:
--   - user@example.com / user123  
--   - jane@example.com / user123
--   - mike@example.com / user123
-- ============================================
