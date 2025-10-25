# Audio Analysis System

A comprehensive PHP-based audio analysis system that provides end-to-end audio processing including transcription, translation, and detailed sentiment analysis using Google's Gemini 2.0 Flash AI.

## Features

### 🎵 Audio Processing
- **Multi-format Support**: MP3, WAV, M4A, AAC, OGG, WebM, FLAC (with MIME type variations)
- **Large File Handling**: Up to 50MB file uploads
- **Secure Upload**: File validation and sanitization

### 🗣️ Transcription
- **Original Language Transcription**: Maintains original language with timestamps
- **Speaker Identification**: Automatic speaker detection and labeling
- **Timestamp Accuracy**: Precise timing for each speech segment

### 🌍 Translation
- **English Translation**: Automatic translation while preserving format
- **Format Preservation**: Maintains timestamps and speaker labels

### 💭 Sentiment Analysis
- **Speaker-Level Analysis**: Individual sentiment breakdown per speaker
- **Overall Sentiment**: Comprehensive conversation analysis
- **Detailed Metrics**: Confidence scores, emotion detection, tone analysis

### 🎯 Agent Performance Insights
- **Clarity Scoring**: Communication effectiveness measurement
- **Empathy Assessment**: Emotional intelligence evaluation
- **Professionalism Rating**: Professional conduct analysis
- **Actionable Recommendations**: Specific improvement suggestions

## Installation

### Prerequisites
- PHP 7.4 or higher
- CURL extension enabled
- FileInfo extension enabled
- JSON extension enabled
- Web server (Apache/Nginx)

### Setup Steps

1. **Clone/Download** the files to your web server directory
2. **Configure API Keys** in `config.php`:
   ```php
   define('GEMINI_API_KEYS', [
       'your-gemini-api-key-1',
       'your-gemini-api-key-2',
       'your-gemini-api-key-3'
   ]);
   ```
3. **Set Permissions**:
   ```bash
   chmod 755 uploads/
   chmod 755 logs/
   ```
4. **Test Installation**: Visit `test.php` to run system diagnostics

## Usage

### Web Interface
1. Navigate to `index.php` in your browser
2. Upload an audio file using the form
3. Wait for processing to complete
4. View comprehensive analysis results

### API Endpoint
Send POST requests to `api.php` with audio file:

```bash
curl -X POST \
  -F "audio_file=@your-audio.mp3" \
  http://your-domain/trackerbi/api.php
```

#### API Response Format
```json
{
  "success": true,
  "timestamp": "2024-10-12T14:19:00+00:00",
  "data": {
    "upload_info": {
      "filename": "audio_12345.mp3",
      "size_bytes": 1048576,
      "mime_type": "audio/mpeg"
    },
    "transcription": "[00:00:05] Speaker 1: Hello, how can I help you today?",
    "translation": "[00:00:05] Speaker 1: Hello, how can I help you today?",
    "sentiment_analysis": {
      "speaker_analysis": [...],
      "overall_sentiment": {...},
      "sentiment_score": {...},
      "agent_performance": {...}
    }
  }
}
```

## File Structure

```
trackerbi/
├── config.php              # Configuration settings
├── AudioAnalyzer.php       # Core analysis class
├── ErrorHandler.php        # Error handling system
├── index.php              # Web interface
├── api.php                # API endpoint
├── test.php               # System diagnostics
├── README.md              # Documentation
├── uploads/               # Temporary file storage
└── logs/                  # System logs
    ├── audio_analysis.log
    ├── structured_errors.json
    └── api_usage.log
```

## Configuration Options

### API Settings
```php
// Multiple API keys for load balancing
define('GEMINI_API_KEYS', [...]);

// Gemini API endpoint
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent');
```

### File Upload Settings
```php
// Maximum file size (50MB)
define('MAX_FILE_SIZE', 50 * 1024 * 1024);

// Allowed audio formats (includes MIME type variations)
define('ALLOWED_AUDIO_TYPES', [
    'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav',
    'audio/wave', 'audio/x-pn-wav', 'audio/mp4', 'audio/m4a',
    'audio/x-m4a', 'audio/aac', 'audio/ogg', 'audio/x-ogg',
    'audio/webm', 'audio/flac', 'audio/x-flac'
]);
```

## Analysis Output

### 🗣️ Speaker Sentiment Breakdown
- **Individual Analysis**: Per-speaker sentiment classification
- **Confidence Scores**: Reliability metrics for each analysis
- **Emotion Detection**: Key emotions identified in speech
- **Tone Indicators**: Communication style markers

### 💬 Overall Sentiment Summary
- **Primary Sentiment**: Dominant emotional tone
- **Empathy Level**: Emotional intelligence assessment
- **Politeness Level**: Courtesy and respect measurement
- **Frustration/Positive Indicators**: Specific behavioral markers

### 🔢 Sentiment Scoring
- **Numerical Score**: 0-100 scale (0=very negative, 100=very positive)
- **Confidence Rating**: Analysis reliability percentage
- **Scale Reference**: Clear interpretation guidelines

### 🎯 Agent Performance Insights
- **Clarity Score**: Communication effectiveness (0-100)
- **Empathy Score**: Emotional connection rating (0-100)
- **Professionalism Score**: Professional conduct assessment (0-100)
- **Strengths**: Identified positive behaviors
- **Improvement Areas**: Specific development opportunities
- **Recommendations**: Actionable improvement suggestions

## Error Handling

### Comprehensive Logging
- **Error Classification**: Detailed error categorization
- **Stack Traces**: Full debugging information
- **Request Context**: User and request information
- **Structured Logging**: JSON format for analysis

### Error Recovery
- **API Key Rotation**: Automatic failover between keys
- **Retry Logic**: Intelligent retry mechanisms
- **Graceful Degradation**: Partial results when possible

### Monitoring
- **Usage Tracking**: API call statistics
- **Performance Metrics**: Processing time monitoring
- **Error Rates**: System health indicators

## Security Features

### File Upload Security
- **MIME Type Validation**: Strict file type checking
- **File Size Limits**: Prevents resource exhaustion
- **Temporary Storage**: Automatic cleanup after processing
- **Path Sanitization**: Prevents directory traversal

### API Security
- **Input Validation**: Comprehensive request validation
- **Error Information Limiting**: Prevents information disclosure
- **Rate Limiting Ready**: Structure for implementing rate limits

## Troubleshooting

### Common Issues

1. **Upload Fails**
   - Check file size limits in PHP configuration
   - Verify upload directory permissions
   - Ensure supported file format

2. **API Errors**
   - Validate API keys in configuration
   - Check internet connectivity
   - Review error logs for details

3. **Processing Timeouts**
   - Increase PHP max_execution_time
   - Check file size and complexity
   - Monitor server resources

### Diagnostic Tools
- Run `test.php` for comprehensive system check
- Check logs in `logs/` directory
- Monitor API usage patterns

## Performance Optimization

### Recommendations
- **API Key Rotation**: Distribute load across multiple keys
- **File Cleanup**: Regular cleanup of temporary files
- **Log Rotation**: Implement log file rotation
- **Caching**: Consider caching for repeated analyses

### Scaling Considerations
- **Queue System**: For high-volume processing
- **Load Balancing**: Multiple server instances
- **Database Storage**: For persistent result storage

## Support

### Getting Help
1. Check the diagnostic output from `test.php`
2. Review error logs in `logs/audio_analysis.log`
3. Verify API key validity and quotas
4. Ensure all PHP extensions are installed

### System Requirements
- PHP 7.4+ with CURL, FileInfo, JSON extensions
- Minimum 256MB memory limit
- Internet connectivity for Gemini API
- Write permissions for uploads and logs directories

## License

This project is provided as-is for educational and commercial use. Please ensure compliance with Google's Gemini 2.0 Flash API terms of service.

## Version History

- **v1.0.0**: Initial release with full feature set
  - Audio upload and validation
  - Transcription with timestamps
  - English translation
  - Comprehensive sentiment analysis
  - Agent performance insights
  - Error handling and logging
  - Web interface and API endpoint
"# trackerbi" 
"# trackerbi" 
"# trackerbi" 
"# trackerbi" 
"# trackerbi" 
