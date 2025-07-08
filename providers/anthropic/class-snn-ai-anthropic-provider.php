<?php
/**
 * Anthropic provider for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Anthropic_Provider extends SNN_AI_Abstract_Provider {
    
    protected $name = 'anthropic';
    protected $display_name = 'Anthropic';
    protected $description = 'Anthropic Claude models for advanced reasoning and conversation';
    
    public function __construct($settings = []) {
        parent::__construct($settings);
        $this->capabilities = [
            'chat' => true,
            'completion' => true,
            'embeddings' => false,
            'images' => false,
            'streaming' => true,
            'function_calling' => true
        ];
    }
    
    protected function init_models() {
        $this->models = [
            'claude-3-opus-20240229' => [
                'name' => 'Claude 3 Opus',
                'description' => 'Most capable Claude model for complex tasks',
                'context_window' => 200000,
                'max_tokens' => 4096,
                'type' => 'chat'
            ],
            'claude-3-sonnet-20240229' => [
                'name' => 'Claude 3 Sonnet',
                'description' => 'Balanced performance and speed',
                'context_window' => 200000,
                'max_tokens' => 4096,
                'type' => 'chat'
            ],
            'claude-3-haiku-20240307' => [
                'name' => 'Claude 3 Haiku',
                'description' => 'Fast and efficient Claude model',
                'context_window' => 200000,
                'max_tokens' => 4096,
                'type' => 'chat'
            ]
        ];
        
        $this->default_model = 'claude-3-sonnet-20240229';
        
        $this->pricing = [
            'claude-3-opus-20240229' => [
                'input' => 0.000015,
                'output' => 0.000075
            ],
            'claude-3-sonnet-20240229' => [
                'input' => 0.000003,
                'output' => 0.000015
            ],
            'claude-3-haiku-20240307' => [
                'input' => 0.00000025,
                'output' => 0.00000125
            ]
        ];
    }
    
    protected function get_default_endpoint() {
        return 'https://api.anthropic.com/v1';
    }
    
    protected function get_default_settings() {
        return [
            'api_key' => '',
            'version' => '2023-06-01',
            'timeout' => 60,
            'max_retries' => 3
        ];
    }
    
    public function chat($args) {
        $defaults = [
            'model' => $this->default_model,
            'messages' => [],
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'top_p' => 1,
            'stop_sequences' => null,
            'stream' => false,
            'system' => null
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        if (!$this->validate_model($args['model'])) {
            return new WP_Error('invalid_model', __('Invalid model specified', 'snn-ai-api-system'));
        }
        
        $args['messages'] = $this->sanitize_messages($args['messages']);
        
        if (empty($args['messages'])) {
            return new WP_Error('empty_messages', __('Messages cannot be empty', 'snn-ai-api-system'));
        }
        
        // Convert OpenAI format to Anthropic format
        $anthropic_messages = $this->convert_messages_to_anthropic($args['messages']);
        
        $data = [
            'model' => $args['model'],
            'messages' => $anthropic_messages,
            'max_tokens' => intval($args['max_tokens']),
            'temperature' => floatval($args['temperature']),
            'top_p' => floatval($args['top_p']),
            'stream' => boolval($args['stream'])
        ];
        
        if ($args['system']) {
            $data['system'] = wp_kses_post($args['system']);
        }
        
        if ($args['stop_sequences']) {
            $data['stop_sequences'] = is_array($args['stop_sequences']) ? $args['stop_sequences'] : [$args['stop_sequences']];
        }
        
        $response = $this->make_request('/messages', 'POST', $data);
        
        $this->log_request('/messages', $data, $response);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $this->format_chat_response($response);
    }
    
    public function complete($args) {
        $defaults = [
            'model' => $this->default_model,
            'prompt' => '',
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'top_p' => 1,
            'stop_sequences' => null
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        if (empty($args['prompt'])) {
            return new WP_Error('empty_prompt', __('Prompt cannot be empty', 'snn-ai-api-system'));
        }
        
        // Convert completion to chat format for Anthropic
        $chat_args = [
            'model' => $args['model'],
            'messages' => [
                ['role' => 'user', 'content' => $args['prompt']]
            ],
            'max_tokens' => $args['max_tokens'],
            'temperature' => $args['temperature'],
            'top_p' => $args['top_p'],
            'stop_sequences' => $args['stop_sequences']
        ];
        
        $result = $this->chat($chat_args);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        // Convert chat response to completion format
        return [
            'text' => $result['content'] ?? '',
            'finish_reason' => $result['finish_reason'] ?? null,
            'usage' => $result['usage'] ?? null,
            'model' => $result['model'] ?? null,
            'created' => $result['created'] ?? null,
            'id' => $result['id'] ?? null
        ];
    }
    
    public function embed($text, $args = []) {
        return new WP_Error('not_supported', __('Embeddings not supported by Anthropic', 'snn-ai-api-system'));
    }
    
    public function generate_image($args) {
        return new WP_Error('not_supported', __('Image generation not supported by Anthropic', 'snn-ai-api-system'));
    }
    
    protected function get_headers() {
        return [
            'Content-Type' => 'application/json',
            'x-api-key' => $this->api_key,
            'anthropic-version' => $this->settings['version'],
            'User-Agent' => 'SNN-AI-API-System/' . SNN_AI_API_SYSTEM_VERSION
        ];
    }
    
    private function convert_messages_to_anthropic($messages) {
        $anthropic_messages = [];
        
        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                // System messages are handled separately in Anthropic
                continue;
            }
            
            $anthropic_messages[] = [
                'role' => $message['role'],
                'content' => $message['content']
            ];
        }
        
        return $anthropic_messages;
    }
    
    private function format_chat_response($response) {
        $content = '';
        
        if (isset($response['content']) && is_array($response['content'])) {
            foreach ($response['content'] as $block) {
                if ($block['type'] === 'text') {
                    $content .= $block['text'];
                }
            }
        }
        
        return [
            'content' => $content,
            'role' => $response['role'] ?? 'assistant',
            'function_call' => null,
            'finish_reason' => $response['stop_reason'] ?? null,
            'usage' => $response['usage'] ?? null,
            'model' => $response['model'] ?? null,
            'created' => time(),
            'id' => $response['id'] ?? null
        ];
    }
    
    public function get_settings_schema() {
        $schema = parent::get_settings_schema();
        
        $schema['version'] = [
            'type' => 'string',
            'label' => __('API Version', 'snn-ai-api-system'),
            'description' => __('Anthropic API version', 'snn-ai-api-system'),
            'default' => '2023-06-01',
            'required' => false
        ];
        
        return $schema;
    }
}