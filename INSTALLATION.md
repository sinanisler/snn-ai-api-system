# Installation Guide

## Quick Installation

1. **Upload Plugin**
   - Download the plugin files
   - Upload to `/wp-content/plugins/snn-ai-api-system/`
   - Activate via WordPress Admin > Plugins

2. **Configure API Keys**
   - Go to **SNN AI > Providers**
   - Add your API keys for desired providers
   - Test connections

3. **Start Using**
   - Visit **SNN AI > Chat Interface** to test
   - Use PHP functions: `snn_ai_chat()`, `snn_ai_complete()`
   - Use JavaScript API: `wp.snnAI.chat()`

## Detailed Installation

### 1. System Requirements

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+
- 256MB RAM minimum
- cURL extension enabled
- OpenSSL extension enabled

### 2. Download & Upload

```bash
# Via FTP/SFTP
unzip snn-ai-api-system.zip
upload snn-ai-api-system/ to /wp-content/plugins/

# Via WordPress Admin
Plugins > Add New > Upload Plugin > Choose File
```

### 3. Activation

- WordPress Admin > Plugins
- Find "SNN AI API System"
- Click "Activate"

### 4. Initial Configuration

#### Database Setup
Plugin automatically creates required tables on activation:
- `wp_snn_ai_providers`
- `wp_snn_ai_sessions`
- `wp_snn_ai_templates`
- `wp_snn_ai_logs`
- `wp_snn_ai_cache`
- `wp_snn_ai_usage`

#### Provider Setup
1. **OpenAI**
   - Get API key from https://platform.openai.com/
   - Add to SNN AI > Providers > OpenAI
   - Test connection

2. **OpenRouter**
   - Get API key from https://openrouter.ai/
   - Add to SNN AI > Providers > OpenRouter
   - Test connection

3. **Anthropic**
   - Get API key from https://console.anthropic.com/
   - Add to SNN AI > Providers > Anthropic
   - Test connection

### 5. Configuration via wp-config.php

```php
// Add to wp-config.php
define('SNN_AI_OPENAI_API_KEY', 'sk-your-key');
define('SNN_AI_OPENROUTER_API_KEY', 'sk-or-your-key');
define('SNN_AI_ANTHROPIC_API_KEY', 'sk-ant-your-key');
```

### 6. Testing Installation

1. **Admin Interface Test**
   - Go to SNN AI > Chat Interface
   - Select a provider and model
   - Send a test message

2. **API Test**
   ```php
   $response = snn_ai_chat([
       'provider' => 'openai',
       'model' => 'gpt-3.5-turbo',
       'messages' => [['role' => 'user', 'content' => 'Hello!']]
   ]);
   var_dump($response);
   ```

3. **JavaScript Test**
   ```javascript
   wp.snnAI.chat({
       provider: 'openai',
       model: 'gpt-3.5-turbo',
       messages: [{role: 'user', content: 'Hello!'}]
   }).then(console.log);
   ```

## Troubleshooting

### Common Issues

1. **Plugin Not Activating**
   - Check PHP version (7.4+ required)
   - Verify file permissions
   - Check error logs

2. **API Keys Not Working**
   - Verify key format and validity
   - Check account limits/billing
   - Test with provider's API directly

3. **Database Errors**
   - Check MySQL version (5.6+ required)
   - Verify database user permissions
   - Check wp-config.php database settings

4. **Permission Errors**
   - Verify user capabilities
   - Check WordPress user roles
   - Review plugin permissions

### Debug Mode

Enable debug mode:
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('SNN_AI_DEBUG_MODE', true);
```

Check logs:
- `/wp-content/debug.log`
- SNN AI > Usage & Logs

### Performance Issues

1. **Slow Responses**
   - Enable caching
   - Optimize database
   - Check network connectivity

2. **Memory Issues**
   - Increase PHP memory limit
   - Use smaller token limits
   - Enable background processing

3. **Rate Limiting**
   - Adjust rate limits
   - Distribute requests
   - Use multiple providers

## Security Considerations

1. **API Key Security**
   - Never commit keys to version control
   - Use environment variables
   - Enable key encryption

2. **User Permissions**
   - Limit admin access
   - Use appropriate capabilities
   - Review user roles regularly

3. **Network Security**
   - Use HTTPS
   - Configure firewall
   - Monitor access logs

## Updating

### Manual Update
1. Backup current plugin
2. Upload new files
3. Activate plugin
4. Check for database updates

### Automatic Update
- WordPress will notify when updates available
- Click "Update Now"
- Test functionality after update

## Uninstalling

### Via WordPress Admin
1. Deactivate plugin
2. Delete plugin files
3. Database tables remain (by design)

### Complete Removal
```sql
-- Remove all plugin data
DROP TABLE wp_snn_ai_providers;
DROP TABLE wp_snn_ai_sessions;
DROP TABLE wp_snn_ai_templates;
DROP TABLE wp_snn_ai_logs;
DROP TABLE wp_snn_ai_cache;
DROP TABLE wp_snn_ai_usage;

-- Remove options
DELETE FROM wp_options WHERE option_name LIKE 'snn_ai_%';
```

## Support

- GitHub Issues: [Repository URL]
- Documentation: README.md
- Email: support@example.com