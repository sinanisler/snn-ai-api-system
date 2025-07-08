<?php
/**
 * OpenRouter provider for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_OpenRouter_Provider extends SNN_AI_Abstract_Provider {
    
    protected $name = 'openrouter';
    protected $display_name = 'OpenRouter';
    protected $description = 'Access to 400+ AI models via OpenRouter API';
    
    public function __construct($settings = []) {
        parent::__construct($settings);
        $this->capabilities = [
            'chat' => true,
            'completion' => true,
            'embeddings' => false,
            'images' => true,
            'streaming' => true,
            'function_calling' => true
        ];
    }
    
    protected function init_models() {
        // Popular models - in production, this would be dynamically fetched
        $this->models = [
            'anthropic/claude-3-opus' => [
                'name' => 'Claude 3 Opus',
                'description' => 'Most capable Claude model',
                'context_window' => 200000,
                'type' => 'chat'
            ],
            'anthropic/claude-3-sonnet' => [
                'name' => 'Claude 3 Sonnet',
                'description' => 'Balanced Claude model',
                'context_window' => 200000,
                'type' => 'chat'
            ],
            'anthropic/claude-3-haiku' => [
                'name' => 'Claude 3 Haiku',
                'description' => 'Fast Claude model',
                'context_window' => 200000,
                'type' => 'chat'
            ],
            'openai/gpt-4-turbo' => [
                'name' => 'GPT-4 Turbo',
                'description' => 'OpenAI GPT-4 Turbo via OpenRouter',
                'context_window' => 128000,
                'type' => 'chat'
            ],
            'openai/gpt-3.5-turbo' => [
                'name' => 'GPT-3.5 Turbo',
                'description' => 'OpenAI GPT-3.5 Turbo via OpenRouter',
                'context_window' => 16384,
                'type' => 'chat'
            ],
            'meta-llama/llama-3-70b-instruct' => [
                'name' => 'Llama 3 70B Instruct',
                'description' => 'Meta Llama 3 70B instruction model',
                'context_window' => 8192,
                'type' => 'chat'
            ],
            'mistralai/mistral-large' => [
                'name' => 'Mistral Large',
                'description' => 'Mistral AI large model',
                'context_window' => 32768,
                'type' => 'chat'
            ],
            'google/gemini-pro' => [
                'name' => 'Gemini Pro',
                'description' => 'Google Gemini Pro model',
                'context_window' => 30720,
                'type' => 'chat'
            ],
            'stability-ai/stable-diffusion-xl' => [
                'name' => 'Stable Diffusion XL',
                'description' => 'Stability AI image generation',
                'type' => 'image'
            ]
        ];
        
        $this->default_model = 'anthropic/claude-3-haiku';
        
        // Pricing varies by model - these are examples
        $this->pricing = [
            'anthropic/claude-3-opus' => [
                'input' => 0.000015,
                'output' => 0.000075
            ],
            'anthropic/claude-3-sonnet' => [
                'input' => 0.000003,
                'output' => 0.000015
            ],
            'anthropic/claude-3-haiku' => [
                'input' => 0.00000025,
                'output' => 0.00000125
            ]
        ];
    }
    
    protected function get_default_endpoint() {
        return 'https://openrouter.ai/api/v1';
    }
    
    protected function get_default_settings() {
        return [
            'api_key' => '',
            'site_url' => get_site_url(),
            'app_name' => get_bloginfo('name'),
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
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'stream' => false,
            'provider' => null,
            'route' => 'fallback'
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        if (!$this->validate_model($args['model'])) {
            return new WP_Error('invalid_model', __('Invalid model specified', 'snn-ai-api-system'));
        }
        
        $args['messages'] = $this->sanitize_messages($args['messages']);
        
        if (empty($args['messages'])) {
            return new WP_Error('empty_messages', __('Messages cannot be empty', 'snn-ai-api-system'));
        }
        
        $data = [
            'model' => $args['model'],
            'messages' => $args['messages'],
            'max_tokens' => intval($args['max_tokens']),
            'temperature' => floatval($args['temperature']),
            'top_p' => floatval($args['top_p']),
            'frequency_penalty' => floatval($args['frequency_penalty']),
            'presence_penalty' => floatval($args['presence_penalty']),
            'stream' => boolval($args['stream']),
            'route' => $args['route']
        ];
        
        if ($args['provider']) {
            $data['provider'] = $args['provider'];
        }
        
        $response = $this->make_request('/chat/completions', 'POST', $data);
        
        $this->log_request('/chat/completions', $data, $response);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $this->format_chat_response($response);
    }
    
    public function complete($args) {
        // OpenRouter primarily uses chat completions
        // Convert completion to chat format
        $prompt = $args['prompt'] ?? '';
        
        if (empty($prompt)) {
            return new WP_Error('empty_prompt', __('Prompt cannot be empty', 'snn-ai-api-system'));
        }
        
        $chat_args = [
            'model' => $args['model'] ?? $this->default_model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => $args['max_tokens'] ?? 1000,
            'temperature' => $args['temperature'] ?? 0.7,
            'top_p' => $args['top_p'] ?? 1,
            'frequency_penalty' => $args['frequency_penalty'] ?? 0,
            'presence_penalty' => $args['presence_penalty'] ?? 0
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
        return new WP_Error('not_supported', __('Embeddings not supported by OpenRouter', 'snn-ai-api-system'));
    }
    
    public function generate_image($args) {
        $defaults = [
            'model' => 'stability-ai/stable-diffusion-xl',
            'prompt' => '',
            'size' => '1024x1024',
            'quality' => 'standard',
            'style' => 'vivid',
            'response_format' => 'url',
            'n' => 1
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        if (empty($args['prompt'])) {
            return new WP_Error('empty_prompt', __('Prompt cannot be empty', 'snn-ai-api-system'));
        }
        
        $data = [
            'model' => $args['model'],
            'prompt' => wp_kses_post($args['prompt']),
            'size' => $args['size'],
            'quality' => $args['quality'],
            'style' => $args['style'],
            'response_format' => $args['response_format'],
            'n' => intval($args['n'])
        ];
        
        $response = $this->make_request('/images/generations', 'POST', $data);
        
        $this->log_request('/images/generations', $data, $response);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $this->format_image_response($response);
    }
    
    public function get_available_models() {
        // Fetch current models from OpenRouter API
        $response = $this->make_request('/models', 'GET');
        
        if (is_wp_error($response)) {
            return $this->models; // Return cached models on error
        }
        
        $models = [];
        
        foreach ($response['data'] as $model) {
            $models[$model['id']] = [
                'name' => $model['name'] ?? $model['id'],
                'description' => $model['description'] ?? '',
                'context_window' => $model['context_length'] ?? 0,
                'type' => 'chat',
                'pricing' => $model['pricing'] ?? []
            ];
        }
        
        return $models;
    }
    
    protected function get_headers() {
        $headers = parent::get_headers();
        
        // OpenRouter specific headers
        $headers['HTTP-Referer'] = $this->settings['site_url'];
        $headers['X-Title'] = $this->settings['app_name'];
        
        return $headers;
    }
    
    private function format_chat_response($response) {
        $choice = $response['choices'][0] ?? null;
        
        if (!$choice) {
            return new WP_Error('invalid_response', __('Invalid response format', 'snn-ai-api-system'));
        }
        
        return [
            'content' => $choice['message']['content'] ?? '',
            'role' => $choice['message']['role'] ?? 'assistant',
            'function_call' => $choice['message']['function_call'] ?? null,
            'finish_reason' => $choice['finish_reason'] ?? null,
            'usage' => $response['usage'] ?? null,
            'model' => $response['model'] ?? null,
            'created' => $response['created'] ?? null,
            'id' => $response['id'] ?? null,
            'provider' => $response['provider'] ?? null
        ];
    }
    
    private function format_image_response($response) {
        $images = $response['data'] ?? [];
        
        if (empty($images)) {
            return new WP_Error('invalid_response', __('Invalid response format', 'snn-ai-api-system'));
        }
        
        return [
            'images' => $images,
            'created' => $response['created'] ?? null
        ];
    }
    
    public function get_settings_schema() {
        $schema = parent::get_settings_schema();
        
        $schema['site_url'] = [
            'type' => 'url',
            'label' => __('Site URL', 'snn-ai-api-system'),
            'description' => __('Your website URL for OpenRouter', 'snn-ai-api-system'),
            'default' => get_site_url(),
            'required' => false
        ];
        
        $schema['app_name'] = [
            'type' => 'string',
            'label' => __('App Name', 'snn-ai-api-system'),
            'description' => __('Your app name for OpenRouter', 'snn-ai-api-system'),
            'default' => get_bloginfo('name'),
            'required' => false
        ];
        
        return $schema;
    }
}