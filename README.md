# SNN AI API System

## AI Integration for WordPress: Multi-Provider Support with Contextual Data Intelligence

The SNN AI API System is a robust WordPress plugin engineered to integrate leading Artificial Intelligence models directly into your website environment. This plugin provides a streamlined interface for connecting to providers like OpenAI, OpenRouter, and Anthropic, allowing WordPress users and developers to leverage advanced AI capabilities without extensive custom coding. A core feature is its **data injection system**, which dynamically pulls WordPress content and inserts it into AI prompts, enabling contextual and relevant AI interactions for various applications such as content generation, intelligent chatbots, and data analysis.

This plugin is designed for performance, security, and extensibility, offering a solid foundation for AI-powered features within any WordPress site. It serves as a practical demonstration of integrating complex AI APIs with a content management system, emphasizing efficient data handling and a modular architecture.


-----

## Features

The SNN AI API System offers key features for effective AI integration:

  * **Multi-Provider Connectivity**: Connects to **OpenAI**, **OpenRouter**, and **Anthropic** APIs, with a design that supports additional custom providers.
  * **Contextual Data Injection**: Dynamically feeds WordPress data (e.g., posts, users, terms, custom fields) into AI prompts, enhancing response relevance.
  * **REST API Integration**: Provides a complete set of REST API endpoints for AI operations, facilitating integration with custom applications, themes, and plugins.
  * **Intuitive Admin Interface**: Features a user-friendly backend for managing AI provider settings, data templates, and monitoring usage.
  * **Integrated Security**: Includes input validation, rate limiting, WordPress capability-based access control, API key encryption, and nonce verification.
  * **Performance Optimization**: Utilizes caching for AI responses and template data, with optimized database interactions for efficient operation.
  * **Comprehensive Usage Tracking**: Logs detailed AI usage data, including request counts, token consumption, estimated costs, and response times for analytical purposes.
  * **Extensible Architecture**: Built with a clean, object-oriented PHP design, offering numerous hooks and filters for deep customization and developer flexibility.

-----

## Installation

### System Requirements

Verify your environment meets these minimum requirements:

  * **WordPress**: Version 5.0 or higher.
  * **PHP**: Version 7.4 or higher.
  * **MySQL**: Version 5.6 or higher.
  * **Memory**: Minimum 256MB PHP memory limit.
  * **PHP Extensions**: `cURL` and `OpenSSL` extensions must be enabled.

### Download & Upload

Choose one of the following methods:

**Via FTP/SFTP:**

1.  Download the plugin `.zip` file from the [GitHub Repository](https://github.com/sinanisler/snn-ai-api-system).
2.  Extract the `snn-ai-api-system.zip` file.
3.  Upload the extracted `snn-ai-api-system` directory to your WordPress installation's `/wp-content/plugins/` folder.

**Via WordPress Admin:**

1.  Navigate to your WordPress Admin Dashboard.
2.  Go to **Plugins** \> **Add New**.
3.  Click the **Upload Plugin** button.
4.  Choose the `snn-ai-api-system.zip` file and click **Install Now**.

### Activation

After uploading:

1.  In your WordPress Admin, go to **Plugins**.
2.  Find "SNN AI API System" and click **Activate**.

Upon activation, the plugin automatically sets up the following database tables:

  * `wp_snn_ai_providers`
  * `wp_snn_ai_sessions`
  * `wp_snn_ai_templates`
  * `wp_snn_ai_logs`
  * `wp_snn_ai_cache`
  * `wp_snn_ai_usage`

### Initial Configuration

Configure your AI provider API keys:

1.  In your WordPress admin menu, go to **SNN AI** \> **Providers**.
2.  For **OpenAI**: Obtain your API key from [https://platform.openai.com/](https://platform.openai.com/). Enter it and optionally your Organization ID.
3.  For **OpenRouter**: Obtain your API key from [https://openrouter.ai/](https://openrouter.ai/). Enter it.
4.  For **Anthropic**: Obtain your API key from [https://console.anthropic.com/](https://console.anthropic.com/). Enter it.
5.  **Test Connection** for each provider to ensure correct setup.

### Configuration via wp-config.php

For enhanced security and environment management, define API keys and other settings in your `wp-config.php` file.

**Example `wp-config.php` entries:**

```php
// --- AI Provider API Keys ---

// OpenAI Configuration
define('SNN_AI_OPENAI_API_KEY', 'sk-your-openai-api-key-here');
define('SNN_AI_OPENAI_ORGANIZATION', 'your-openai-org-id'); // Optional

// OpenRouter Configuration
define('SNN_AI_OPENROUTER_API_KEY', 'sk-or-your-openrouter-key-here');

// Anthropic Configuration
define('SNN_AI_ANTHROPIC_API_KEY', 'sk-ant-your-anthropic-key-here');

// --- Plugin Settings ---

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

// --- Advanced Settings ---

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
```

You can also use environment variables for keys:

```php
// In wp-config.php, assuming environment variables are set externally:
define('SNN_AI_OPENAI_API_KEY', getenv('SNN_AI_OPENAI_API_KEY'));
define('SNN_AI_OPENROUTER_API_KEY', getenv('SNN_AI_OPENROUTER_API_KEY'));
define('SNN_AI_ANTHROPIC_API_KEY', getenv('SNN_AI_ANTHROPIC_API_KEY'));
```

-----

## Quick Start

Start utilizing AI functions directly in your code or via the admin interface.

### Basic PHP Usage

The plugin provides global helper functions for common AI operations:

```php
// Example: Simple chat request
$response = snn_ai_chat([
    'provider' => 'openai',
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        ['role' => 'user', 'content' => 'Hello!']
    ]
]);

if (is_wp_error($response)) {
    error_log('AI Chat Error: ' . $response->get_error_message());
} else {
    echo 'AI says: ' . $response['content'];
}

// Example: Text completion
$response = snn_ai_complete([
    'provider' => 'anthropic',
    'model' => 'claude-3-sonnet-20240229',
    'prompt' => 'Write a short paragraph about the benefits of AI in content creation.',
    'max_tokens' => 200
]);

if (is_wp_error($response)) {
    error_log('AI Completion Error: ' . $response->get_error_message());
} else {
    echo 'AI completion: ' . $response['text'];
}

// Example: Generate embeddings
$response = snn_ai_embed('This is a sample text for embedding.', [
    'provider' => 'openai',
    'model' => 'text-embedding-3-small'
]);

if (is_wp_error($response)) {
    error_log('AI Embeddings Error: ' . $response->get_error_message());
} else {
    echo 'Embedding vector size: ' . count($response['embedding']);
}

// Example: Generate an image
$response = snn_ai_generate_image([
    'provider' => 'openai',
    'model' => 'dall-e-3',
    'prompt' => 'A serene digital painting of a Japanese garden with cherry blossoms.',
    'size' => '1024x1024'
]);

if (is_wp_error($response)) {
    error_log('AI Image Generation Error: ' . $response->get_error_message());
} else {
    echo 'Generated image URL: ' . $response['images'][0]['url'];
}
```

### JavaScript Usage

The plugin exposes a global `wp.snnAI` object for front-end API interactions. This requires WordPress's `wp-api-fetch` and `wp-nonce` scripts to be enqueued.

```javascript
// Example: Chat request
wp.snnAI.chat({
    provider: 'openrouter',
    model: 'anthropic/claude-3-haiku',
    messages: [
        {role: 'user', content: 'What is the capital of France?'}
    ]
}).then(response => {
    console.log('Chat Response:', response.content);
}).catch(error => {
    console.error('Chat API Error:', error.message);
});

// Example: Image generation
wp.snnAI.generateImage({
    provider: 'openai',
    model: 'dall-e-3',
    prompt: 'A minimalistic landscape with a single tree under a starry sky.',
    size: '1024x1024'
}).then(response => {
    console.log('Generated Image URLs:', response.images);
}).catch(error => {
    console.error('Image Generation API Error:', error.message);
});
```

-----

## Data Injection System

This system allows you to feed dynamic WordPress data into AI prompts, ensuring context-aware responses.

### Creating Templates

Define data templates programmatically to specify what data to retrieve and how to format it for AI consumption.

```php
$plugin = SNN_AI_API_System::get_instance();
$data_injector = $plugin->get_data_injector();

// Define a template to fetch recent WordPress posts
$template_id = $data_injector->create_template('Recent Blog Posts Context', [
    'description' => 'Retrieves the titles and excerpts of the 5 most recent published posts.',
    'query_config' => [
        'query_type' => 'posts', // Specifies the type of data to query (posts, terms, users, meta)
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC'
    ],
    'dynamic_tags' => [ // Maps data fields to dynamic tags for use in template_content
        'post_title' => 'get_the_title',
        'post_excerpt' => 'get_the_excerpt',
        'post_date' => 'get_the_date("Y-m-d")',
        'post_author' => 'get_the_author'
    ],
    'template_content' => '
    Here are some recent blog posts from our website:
    {{#posts}}
    Title: {{post_title}}
    Author: {{post_author}}
    Date: {{post_date}}
    Excerpt: {{post_excerpt}}
    ---
    {{/posts}}
    '
]);

if ($template_id) {
    echo "Template 'Recent Blog Posts Context' created with ID: " . $template_id;
} else {
    echo "Failed to create template.";
}
```

### Using Templates

Once a template exists, inject its content into your AI requests to provide context.

```php
$plugin = SNN_AI_API_System::get_instance();
$data_injector = $plugin->get_data_injector();

// Assuming template ID 1 is 'Recent Blog Posts Context'
$injected_data = $data_injector->inject_data(1);

if (is_wp_error($injected_data)) {
    error_log('Data Injection Error: ' . $injected_data->get_error_message());
} else {
    // Use the generated content in a system or user message for AI
    $response = snn_ai_chat([
        'provider' => 'openai',
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => $injected_data['content']],
            ['role' => 'user', 'content' => 'Summarize the provided blog posts in a concise manner, highlighting key topics.']
        ]
    ]);

    if (is_wp_error($response)) {
        error_log('AI Chat Error with context: ' . $response->get_error_message());
    } else {
        echo 'AI Summary with context: ' . $response['content'];
    }
}
```

-----

## API Endpoints

The SNN AI API System exposes REST API endpoints under the `/wp-json/snn-ai/v1/` namespace. These endpoints are designed for integration with headless WordPress setups, custom front-ends, or other applications. All requests to these endpoints require proper WordPress authentication (e.g., nonce for logged-in users).

### Chat Completion

**Method:** `POST`
**Path:** `/wp-json/snn-ai/v1/chat`

**Request Body Example:**

```json
{
    "provider": "openai",
    "model": "gpt-3.5-turbo",
    "messages": [
        {"role": "system", "content": "You are a helpful assistant."},
        {"role": "user", "content": "Tell me a joke."}
    ],
    "max_tokens": 150,
    "temperature": 0.7
}
```

### Text Completion

**Method:** `POST`
**Path:** `/wp-json/snn-ai/v1/complete`

**Request Body Example:**

```json
{
    "provider": "anthropic",
    "model": "claude-3-sonnet-20240229",
    "prompt": "Once upon a time in a land far away, there was a young wizard who",
    "max_tokens": 100,
    "temperature": 0.9
}
```

### Embeddings

**Method:** `POST`
**Path:** `/wp-json/snn-ai/v1/embed`

**Request Body Example:**

```json
{
    "provider": "openai",
    "model": "text-embedding-3-small",
    "text": "The quick brown fox jumps over the lazy dog."
}
```

### Image Generation

**Method:** `POST`
**Path:** `/wp-json/snn-ai/v1/generate-image`

**Request Body Example:**

```json
{
    "provider": "openai",
    "model": "dall-e-3",
    "prompt": "An astronaut riding a horse on the moon, photorealistic.",
    "size": "1024x1024",
    "quality": "hd",
    "n": 1
}
```

-----

## Supported Providers

The SNN AI API System includes built-in integrations for several popular AI providers:

### OpenAI

  * **Models**: Supports current and legacy models for chat (e.g., GPT-4, GPT-3.5 Turbo), text embeddings (e.g., `text-embedding-3-small`), and image generation (e.g., `DALL-E 3`).
  * **Capabilities**: Provides access to chat completion, text completion, embeddings, and image generation. Supports streaming and function calling.
  * **Configuration**: Requires an OpenAI API key. An optional Organization ID can be provided for organizational billing.

### OpenRouter

  * **Models**: Offers a wide array of over 400 models from various developers (e.g., Claude, GPT, Llama, Mistral, Stable Diffusion), all accessible through a single API key.
  * **Capabilities**: Supports chat completion and image generation. Text completion is handled by converting prompts to a chat format. Supports streaming and function calling.
  * **Configuration**: Requires an OpenRouter API key. Site URL and App Name can be provided for OpenRouter's usage tracking.

### Anthropic

  * **Models**: Integrates with Anthropic's Claude family of models (e.g., Claude 3 Opus, Sonnet, Haiku), known for their advanced reasoning.
  * **Capabilities**: Supports chat completion and text completion (by converting prompts to chat messages). Supports streaming and tool use (function calling).
  * **Configuration**: Requires an Anthropic API key and a specified API version (e.g., `2023-06-01`).

-----

## Advanced Features

### Custom Providers

The plugin features an extensible architecture that allows developers to integrate new AI providers. To add a custom provider:

1.  **Extend `SNN_AI_Abstract_Provider`**: Create a new class that extends `SNN_AI_Abstract_Provider` and implements `SNN_AI_Provider_Interface`.
2.  **Implement Methods**: Override the abstract methods (`chat`, `complete`, `embed`, `generate_image`, `get_models`, `test_connection`) to interact with your custom AI service.
3.  **Register Provider**: Use the `snn_ai_register_providers` action hook to register your new provider with the plugin's provider manager.

**Example of registering a custom provider:**

```php
// Assuming My_Custom_Provider class is defined and loaded
add_action('snn_ai_register_providers', function($manager) {
    if (class_exists('My_Custom_Provider')) {
        $manager->register_provider('my-custom-provider', new My_Custom_Provider([
            'api_key' => 'your-custom-api-key',
            'endpoint_url' => 'https://api.mycustomservice.com/v1'
        ]));
    }
});
```

### Custom Data Sources

Extend the data injection system to pull data from sources beyond standard WordPress queries.

1.  **Create Data Source Class**: Implement a class with a `get_data($query_args)` method that returns an array of structured data.
2.  **Register Data Source**: Use the `snn_ai_register_data_sources` action hook to register your custom data source.

**Example of registering a custom data source:**

```php
class My_External_Data_Source {
    public function get_data($query_args) {
        // Fetch data from an external API or custom table based on $query_args
        // Example: $data = fetch_from_external_api($query_args['api_endpoint']);
        return [
            ['id' => 1, 'name' => 'Item A', 'value' => 100],
            ['id' => 2, 'name' => 'Item B', 'value' => 200]
        ];
    }
}

add_action('snn_ai_register_data_sources', function($injector) {
    $injector->register_source('external_items', new My_External_Data_Source());
});

// You can then use 'query_type' => 'external_items' in your data templates.
```

### Hooks and Filters

The plugin provides a rich set of hooks and filters for granular control over its functionality.

**Examples:**

```php
// Filter to modify AI responses globally before they are returned
add_filter('snn_ai_response', function($response, $provider_name, $request_args) {
    if (!is_wp_error($response) && isset($response['content'])) {
        $response['content'] = 'Processed by SNN AI: ' . $response['content'];
    }
    return $response;
}, 10, 3);

// Action to register custom dynamic tags for the data injection system
add_action('snn_ai_register_dynamic_tags', function($dynamic_tags_instance) {
    $dynamic_tags_instance->register_tag('site_tagline', function() {
        return get_bloginfo('description');
    });
});

// Filter to modify the data context available to templates
add_filter('snn_ai_data_context', function($context, $query_config, $dynamic_tags_instance) {
    $context['current_user_display_name'] = wp_get_current_user()->display_name;
    return $context;
}, 10, 3);
```

-----

## Security Considerations

Security is a primary focus for the SNN AI API System. Key measures include:

  * **Input Validation and Sanitization**: All incoming data is rigorously validated and sanitized to prevent common web vulnerabilities like XSS and SQL injection.
  * **Rate Limiting**: Configurable rate limits help protect your AI provider accounts from excessive usage and potential abuse.
  * **WordPress Capability Integration**: Access to administrative features and sensitive API endpoints is controlled through standard WordPress user capabilities, ensuring only authorized users can perform certain actions.
  * **API Key Encryption**: The plugin offers the option to encrypt API keys when stored in the database. For maximum security, it's highly recommended to use **environment variables** for API keys, preventing them from being stored directly in any file or database.
  * **Nonce Verification**: All forms and API requests leverage WordPress nonces for CSRF (Cross-Site Request Forgery) protection.
  * **Comprehensive Audit Logging**: Detailed logs of AI requests, responses, and errors provide an audit trail for security monitoring and troubleshooting.
  * **HTTPS Enforcement**: Always ensure your WordPress site uses **HTTPS** to encrypt all communication, including sensitive API requests to AI providers.
  * **Server-Level Security**: Users should implement proper server security measures, including firewalls and regular monitoring of access logs.

-----

## Performance Optimization

The plugin is designed to operate efficiently within the WordPress environment:

  * **Caching Mechanism**: AI responses and dynamically processed template data can be cached for a configurable duration. This reduces redundant API calls, lowers costs, and improves response times for frequently requested AI operations.
  * **Optimized Database Interactions**: Plugin-specific database tables are designed with efficient queries and proper indexing to minimize database load.
  * **Memory Management**: Configurable PHP memory limits for AI operations help prevent memory exhaustion during complex requests.
  * **Background Processing (Planned)**: Future enhancements may include options for offloading computationally intensive AI tasks to background processes, preventing front-end performance bottlenecks.
  * **CDN Compatibility**: Plugin assets (CSS, JS) are developed to be compatible with Content Delivery Networks for faster loading.

-----

## Usage Analytics

The SNN AI API System provides robust usage tracking to monitor and manage your AI consumption:

  * **Detailed Request Metrics**: Records the number of requests made, input and output tokens consumed, estimated costs per request, and overall response times.
  * **Provider-Specific Analytics**: Provides aggregated usage statistics broken down by individual AI provider and specific model, helping you understand where your resources are being spent.
  * **Error Tracking**: Logs failed AI requests and associated error messages, facilitating quick identification and resolution of integration issues.
  * **User-Based Analytics**: Tracks AI usage by individual users and their respective WordPress roles, offering insights into internal consumption patterns.
  * **Cost Tracking**: Offers estimated costs for each AI interaction and cumulative totals, aiding in budget management and cost optimization.

You can access these statistics and logs via the **SNN AI** \> **Usage & Logs** section in the WordPress admin dashboard.

-----

## Configuration

Most plugin settings can be defined as **WordPress constants** in your `wp-config.php` file. This is the recommended approach for production environments as it allows for version control and environment-specific settings. Refer to the [Configuration via wp-config.php](https://www.google.com/search?q=%23configuration-via-wp-configphp) section for a complete list of available constants.

Additional configurable settings may be accessible through the plugin's administrative interface under **SNN AI** \> **Settings**.

-----

## Testing

The plugin includes a built-in interactive interface for easy testing of AI functionalities:

1.  Navigate to **SNN AI** \> **Chat Interface** in your WordPress admin dashboard.
2.  Select your desired AI provider and model from the dropdowns.
3.  Send test messages or prompts to verify AI responses and functionality.
4.  Observe real-time feedback, including response times and token usage.

For development and debugging, the `validate-plugin.php` script (found in the plugin's root directory) offers basic structural validation for the plugin files outside of the full WordPress environment.

-----

## Troubleshooting

### Common Issues

If you encounter problems, consider these common solutions:

1.  **Plugin Not Activating**:
      * **PHP Version**: Confirm your server's PHP version is 7.4 or newer.
      * **File Permissions**: Check if plugin files and directories have appropriate read/write permissions.
      * **Error Logs**: Examine your WordPress `debug.log` (`/wp-content/debug.log`) and server-level error logs for specific activation failures.
2.  **API Keys Not Working**:
      * **Format and Validity**: Ensure API keys are correctly entered and match the exact format from your AI provider's dashboard.
      * **Account Status**: Verify your AI provider account is active, has sufficient credits, and hasn't hit any usage limits.
      * **Direct Test**: Use `cURL` or Postman to test the AI provider's API directly to isolate issues (e.g., network, key validity).
3.  **Database Errors**:
      * **MySQL Version**: Verify your MySQL version is 5.6 or newer.
      * **Database User Permissions**: Ensure your WordPress database user has `CREATE`, `ALTER`, `INSERT`, `UPDATE`, and `DELETE` permissions on your database.
      * **`wp-config.php`**: Double-check your database connection details in `wp-config.php`.
4.  **Permission Errors (e.g., "You do not have sufficient permissions")**:
      * **User Capabilities**: Confirm the logged-in WordPress user has the necessary capabilities defined by `SNN_AI_ADMIN_CAPABILITY`, `SNN_AI_API_CAPABILITY`, or `SNN_AI_CHAT_CAPABILITY` (defaults are `manage_options` and `edit_posts`).
      * **User Roles**: Review the roles assigned to the affected user.
5.  **Slow Responses**:
      * **Caching**: Ensure `SNN_AI_CACHE_ENABLED` is set to `true` and `SNN_AI_CACHE_DURATION` is appropriate in `wp-config.php`.
      * **Database Optimization**: Periodically optimize your WordPress database tables.
      * **Network Latency**: Check your server's network connectivity and latency to the AI provider's API endpoints.
6.  **Memory Exhaustion Errors**:
      * **PHP Memory Limit**: Increase the PHP memory limit in `wp-config.php` (e.g., `define('WP_MEMORY_LIMIT', '512M');`) or your `php.ini` file.
      * **Token Limits**: Try reducing `SNN_AI_MAX_TOKENS` for AI requests if working with very large prompts/responses.
7.  **Rate Limiting Errors**:
      * **Plugin Limits**: Adjust `SNN_AI_RATE_LIMIT` and `SNN_AI_RATE_LIMIT_PERIOD` constants in `wp-config.php` if you are hitting internal plugin limits.
      * **Provider Limits**: Be aware of the rate limits imposed by the specific AI providers you are using, as these vary and are outside the plugin's control.

### Debug Mode

For detailed logging and debugging, enable WordPress and plugin debug modes in your `wp-config.php` file:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false); // Set to false for production environments
define('SNN_AI_DEBUG_MODE', true); // Enables internal plugin debugging
define('SNN_AI_DEBUG_LOG_REQUESTS', true); // Logs full API request payloads
define('SNN_AI_DEBUG_LOG_RESPONSES', true); // Logs full API response payloads
```

Then, check your logs:

  * **WordPress Debug Log**: `/wp-content/debug.log`
  * **Plugin Logs**: Accessible via **SNN AI** \> **Usage & Logs** in the WordPress admin.

-----

## Updating

Regularly updating the plugin ensures you have the latest features, performance improvements, and security patches.

### Manual Update

1.  **Backup Your Site**: Always create a full backup of your WordPress files and database before any update.
2.  **Download Latest Version**: Get the most recent `snn-ai-api-system.zip` from the [GitHub Repository](https://github.com/sinanisler/snn-ai-api-system).
3.  **Deactivate Plugin**: In your WordPress admin, go to **Plugins** and deactivate "SNN AI API System".
4.  **Replace Files**: Via FTP/SFTP, upload the new plugin files, overwriting the existing `snn-ai-api-system` folder in `/wp-content/plugins/`.
5.  **Reactivate Plugin**: Go back to **Plugins** in WordPress admin and reactivate "SNN AI API System". The plugin will automatically run any necessary database schema updates.
6.  **Verify Functionality**: Test core features to ensure everything is working as expected.

### Automatic Update

If the plugin is listed in the WordPress Plugin Directory, you will receive notifications for updates directly in your WordPress dashboard. Simply click "Update Now" when prompted.

-----

## Uninstalling

### Via WordPress Admin

1.  **Deactivate**: Navigate to **Plugins** in your WordPress admin dashboard. Deactivate "SNN AI API System".
2.  **Delete**: After deactivation, a "Delete" link will appear next to the plugin. Click this link.

*Note: This method will remove the plugin files but **retain** all plugin-related database tables and options by default. This is to preserve your configuration, logs, and usage data if you decide to reinstall the plugin later.*

### Complete Removal (Including Database Data)

To completely remove all plugin data from your database, execute the following SQL queries directly via a database management tool (like phpMyAdmin or Adminer) **after** deactivating and deleting the plugin files through the WordPress admin:

```sql
-- Drop all plugin-specific tables
DROP TABLE IF EXISTS wp_snn_ai_providers;
DROP TABLE IF EXISTS wp_snn_ai_sessions;
DROP TABLE IF EXISTS wp_snn_ai_templates;
DROP TABLE IF EXISTS wp_snn_ai_logs;
DROP TABLE IF EXISTS wp_snn_ai_cache;
DROP TABLE IF EXISTS wp_snn_ai_usage;

-- Delete plugin options from the wp_options table
DELETE FROM wp_options WHERE option_name LIKE 'snn_ai_%';

-- Delete plugin-related user meta data (if any)
DELETE FROM wp_usermeta WHERE meta_key LIKE 'snn_ai_preferences';
```

**Warning:** Always back up your entire database before performing direct SQL operations.
