# Enhanced DuckDuckGo Integration for Shorto Agent

## Overview

The Shorto Agent has been enhanced with improved DuckDuckGo API integration that provides comprehensive search capabilities and URL analysis features. This integration allows the AI agent to:

1. **Search the web** using DuckDuckGo's Instant Answer API
2. **Analyze URLs** for content and optimization recommendations
3. **Generate shortcode suggestions** based on content analysis
4. **Provide security and privacy recommendations** for URLs

## Implementation Details

### 1. Enhanced McpConnector Configuration

The original MCP connector has been enhanced with environment variables for better control:

```php
...McpConnector::make([
    'command' => 'npx',
    'args' => ['-y', '@modelcontextprotocol/server-duckduckgo'],
    'env' => [
        'DDG_SAFE_SEARCH' => 'moderate', // strict, moderate, off
        'DDG_REGION' => 'us-en', // Region setting
    ]
])->tools(),
```

### 2. Custom DuckDuckGo Search Tool

A new `DuckDuckGoSearchTool` class provides direct API access with the following features:

#### API Parameters Supported:
- `q`: Search query
- `format`: Response format (json, xml)
- `no_redirect`: Skip redirects
- `no_html`: Remove HTML from text
- `skip_disambig`: Skip disambiguation

#### Example Usage:
```php
$searchTool = new DuckDuckGoSearchTool();
$results = $searchTool([
    'query' => 'laravel url shortener',
    'format' => 'json',
    'no_redirect' => true,
    'no_html' => true,
    'skip_disambig' => true
]);
```

### 3. URL Content Analysis Tool

The `AnalyzeUrlContentTool` provides comprehensive URL analysis:

#### Features:
- **Content Analysis**: Searches for information about the URL domain and content
- **Shortcode Suggestions**: Generates meaningful shortcode suggestions based on content
- **Security Recommendations**: Checks for HTTPS usage, tracking parameters
- **Optimization Suggestions**: Analyzes URL length and structure

#### Example Usage:
```php
$analysisTool = new AnalyzeUrlContentTool();
$analysis = $analysisTool([
    'originalUrl' => 'https://example.com/very/long/url?utm_source=tracking'
]);
```

## Available Tools in Shorto Agent

### 1. `duckduckgo_search`
Search the web using DuckDuckGo API for URL shortening related queries.

**Parameters:**
- `query` (required): The search query
- `format` (optional): Response format (default: json)
- `no_redirect` (optional): Skip redirects (default: true)
- `no_html` (optional): Remove HTML (default: true)
- `skip_disambig` (optional): Skip disambiguation (default: true)

### 2. `analyze_url_content`
Analyzes URL content using DuckDuckGo search and provides recommendations.

**Parameters:**
- `originalUrl` (required): The URL to analyze

## API Integration Details

### DuckDuckGo Instant Answer API

The integration uses the official DuckDuckGo Instant Answer API:

**Base URL:** `https://api.duckduckgo.com/`

**Example Request:**
```
GET https://api.duckduckgo.com/?q=laravel+url+shortener&format=json&no_redirect=1&no_html=1&skip_disambig=1
```

### Response Processing

The tool processes various types of DuckDuckGo responses:

1. **Abstract**: General information about the search topic
2. **Answer**: Direct answers to queries
3. **Definition**: Dictionary definitions
4. **Related Topics**: Related search results
5. **Search Results**: Web search results

### Error Handling

Comprehensive error handling includes:
- Network timeout protection (30 seconds)
- JSON parsing error handling
- HTTP status code validation
- Detailed error logging

## Security and Privacy Features

### 1. Privacy-First Approach
- Uses DuckDuckGo's privacy-focused search
- No tracking or personal data collection
- Safe search filtering options

### 2. URL Security Analysis
- HTTPS protocol verification
- Tracking parameter detection
- Malicious URL pattern checking

### 3. Content Filtering
- Adult content filtering via safe search
- Spam and malicious content detection

## Configuration

### Environment Variables

Add these to your `.env` file for enhanced functionality:

```env
# DuckDuckGo API Settings
DDG_SAFE_SEARCH=moderate
DDG_REGION=us-en

# Optional: Rate limiting
DDG_REQUEST_TIMEOUT=30
DDG_MAX_REQUESTS_PER_MINUTE=60
```

## Testing

Run the test script to verify the integration:

```bash
php test_duckduckgo_integration.php
```

## Usage Examples

### 1. Search for URL Shortening Information
```php
// The agent can now respond to:
"Search for Laravel URL shortening tutorials"
```

### 2. Analyze a URL Before Shortening
```php
// The agent can now respond to:
"Analyze this URL before shortening: https://example.com/long/path?tracking=params"
```

### 3. Generate Content-Based Shortcodes
```php
// The agent can now respond to:
"Create a meaningful shortcode for this GitHub repository URL"
```

## Benefits

1. **Enhanced User Experience**: Intelligent shortcode suggestions based on content
2. **Security**: Automatic security and privacy recommendations
3. **Content Awareness**: Context-aware URL management
4. **Performance**: Efficient API usage with proper caching and error handling
5. **Privacy**: No tracking or data collection during searches

This enhanced integration makes the Shorto Agent more intelligent and helpful when managing URLs, providing users with valuable insights and recommendations for their shortened links.