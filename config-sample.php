<?php
/**
 * Sample configuration file for SNN AI API System
 * 
 * Copy this file to wp-config.php and update with your API keys
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

// === AI Provider API Keys ===

// OpenAI Configuration
define('SNN_AI_OPENAI_API_KEY', 'sk-your-openai-api-key-here');
define('SNN_AI_OPENAI_ORGANIZATION', 'your-openai-org-id'); // Optional

// OpenRouter Configuration
define('SNN_AI_OPENROUTER_API_KEY', 'sk-or-your-openrouter-key-here');

// Anthropic Configuration
define('SNN_AI_ANTHROPIC_API_KEY', 'sk-ant-your-anthropic-key-here');

// === Plugin Settings ===

// Enable/disable caching
define('SNN_AI_CACHE_ENABLED', true);

// Cache duration in seconds (default: 1800 = 30 minutes)
define('SNN_AI_CACHE_DURATION', 1800);

// Rate limiting settings
define('SNN_AI_RATE_LIMIT', 100); // Requests per period
define('SNN_AI_RATE_LIMIT_PERIOD', 3600); // Period in seconds (1 hour)

// Logging settings
define('SNN_AI_LOG_LEVEL', 'info'); // debug, info, warning, error
define('SNN_AI_LOG_RETENTION_DAYS', 30); // Keep logs for 30 days

// Security settings
define('SNN_AI_ENCRYPT_API_KEYS', true); // Encrypt API keys in database
define('SNN_AI_ENABLE_NONCE_VERIFICATION', true); // Enable CSRF protection

// Performance settings
define('SNN_AI_MAX_TOKENS', 4000); // Maximum tokens per request
define('SNN_AI_REQUEST_TIMEOUT', 60); // Request timeout in seconds
define('SNN_AI_MEMORY_LIMIT', '256M'); // Memory limit for AI operations

// Debug settings (only enable during development)
define('SNN_AI_DEBUG_MODE', false);
define('SNN_AI_DEBUG_LOG_REQUESTS', false);
define('SNN_AI_DEBUG_LOG_RESPONSES', false);

// === Advanced Settings ===

// Custom endpoint URLs (optional)
define('SNN_AI_OPENAI_ENDPOINT', 'https://api.openai.com/v1');
define('SNN_AI_OPENROUTER_ENDPOINT', 'https://openrouter.ai/api/v1');
define('SNN_AI_ANTHROPIC_ENDPOINT', 'https://api.anthropic.com/v1');

// Provider-specific settings
define('SNN_AI_OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo');
define('SNN_AI_OPENROUTER_DEFAULT_MODEL', 'anthropic/claude-3-haiku');
define('SNN_AI_ANTHROPIC_DEFAULT_MODEL', 'claude-3-sonnet-20240229');

// Data injection settings
define('SNN_AI_TEMPLATE_CACHE_ENABLED', true);
define('SNN_AI_TEMPLATE_CACHE_DURATION', 900); // 15 minutes

// WordPress integration settings
define('SNN_AI_ADMIN_CAPABILITY', 'manage_options'); // Required capability for admin access
define('SNN_AI_API_CAPABILITY', 'edit_posts'); // Required capability for API access
define('SNN_AI_CHAT_CAPABILITY', 'edit_posts'); // Required capability for chat interface

// === Usage Examples ===

/*
// Example: Using environment variables instead of constants
// Set these in your server environment:
// SNN_AI_OPENAI_API_KEY=sk-your-key-here
// SNN_AI_OPENROUTER_API_KEY=sk-or-your-key-here
// SNN_AI_ANTHROPIC_API_KEY=sk-ant-your-key-here

// Then use in wp-config.php:
define('SNN_AI_OPENAI_API_KEY', getenv('SNN_AI_OPENAI_API_KEY'));
define('SNN_AI_OPENROUTER_API_KEY', getenv('SNN_AI_OPENROUTER_API_KEY'));
define('SNN_AI_ANTHROPIC_API_KEY', getenv('SNN_AI_ANTHROPIC_API_KEY'));
*/

/*
// Example: Different settings for different environments
if (defined('WP_ENVIRONMENT_TYPE')) {
    switch (WP_ENVIRONMENT_TYPE) {
        case 'development':
            define('SNN_AI_DEBUG_MODE', true);
            define('SNN_AI_RATE_LIMIT', 1000);
            define('SNN_AI_CACHE_ENABLED', false);
            break;
            
        case 'staging':
            define('SNN_AI_DEBUG_MODE', false);
            define('SNN_AI_RATE_LIMIT', 500);
            define('SNN_AI_CACHE_ENABLED', true);
            break;
            
        case 'production':
            define('SNN_AI_DEBUG_MODE', false);
            define('SNN_AI_RATE_LIMIT', 100);
            define('SNN_AI_CACHE_ENABLED', true);
            break;
    }
}
*/

/*
// Example: Custom provider configurations
add_filter('snn_ai_provider_settings', function($settings, $provider) {
    if ($provider === 'openai') {
        $settings['timeout'] = 120; // 2 minutes for OpenAI
        $settings['max_retries'] = 3;
    }
    
    if ($provider === 'anthropic') {
        $settings['timeout'] = 90; // 1.5 minutes for Anthropic
        $settings['version'] = '2023-06-01';
    }
    
    return $settings;
}, 10, 2);
*/

/*
// Example: Custom model configurations
add_filter('snn_ai_model_settings', function($settings, $provider, $model) {
    if ($provider === 'openai' && $model === 'gpt-4') {
        $settings['max_tokens'] = 8000;
        $settings['temperature'] = 0.8;
    }
    
    return $settings;
}, 10, 3);
*/