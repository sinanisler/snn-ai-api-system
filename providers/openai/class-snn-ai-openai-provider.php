<?php
/**
 * OpenAI provider for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_OpenAI_Provider extends SNN_AI_Abstract_Provider {
    
    protected $name = 'openai';
    protected $display_name = 'OpenAI';
    protected $description = 'OpenAI GPT models for chat completion, text completion, and embeddings';
    
    public function __construct($settings = []) {
        parent::__construct($settings);
        $this->capabilities = [
            'chat' => true,
            'completion' => true,
            'embeddings' => true,
            'images' => true,
            'streaming' => true,
            'function_calling' => true
        ];
    }
    
    protected function init_models() {
        $this->models = [
            'gpt-4' => [
                'name' => 'GPT-4',
                'description' => 'Most capable GPT-4 model',
                'context_window' => 8192,
                'max_tokens' => 4096,
                'type' => 'chat'
            ],
            'gpt-4-turbo' => [
                'name' => 'GPT-4 Turbo',
                'description' => 'GPT-4 Turbo with improved speed and efficiency',
                'context_window' => 128000,
                'max_tokens' => 4096,
                'type' => 'chat'
            ],
            'gpt-3.5-turbo' => [
                'name' => 'GPT-3.5 Turbo',
                'description' => 'Fast and efficient GPT-3.5 model',
                'context_window' => 16384,
                'max_tokens' => 4096,
                'type' => 'chat'
            ],
            'text-embedding-3-large' => [
                'name' => 'Text Embedding 3 Large',
                'description' => 'High-quality text embeddings',
                'dimensions' => 3072,
                'type' => 'embedding'
            ],
            'text-embedding-3-small' => [
                'name' => 'Text Embedding 3 Small',
                'description' => 'Efficient text embeddings',
                'dimensions' => 1536,
                'type' => 'embedding'
            ],
            'dall-e-3' => [
                'name' => 'DALL-E 3',
                'description' => 'Advanced image generation',
                'type' => 'image'
            ]
        ];
        
        $this->default_model = 'gpt-3.5-turbo';
        
        $this->pricing = [
            'gpt-4' => [
                'input' => 0.00003,
                'output' => 0.00006
            ],
            'gpt-4-turbo' => [
                'input' => 0.00001,
                'output' => 0.00003
            ],
            'gpt-3.5-turbo' => [
                'input' => 0.0000005,
                'output' => 0.0000015
            ]
        ];
    }
    
    protected function get_default_endpoint() {
        return 'https://api.openai.com/v1';
    }
    
    protected function get_default_settings() {
        return [
            'api_key' => '',
            'organization' => '',
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
            'functions' => null,
            'function_call' => null
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
            'stream' => boolval($args['stream'])
        ];
        
        if ($args['functions']) {
            $data['functions'] = $args['functions'];
        }
        
        if ($args['function_call']) {
            $data['function_call'] = $args['function_call'];
        }
        
        $response = $this->make_request('/chat/completions', 'POST', $data);
        
        $this->log_request('/chat/completions', $data, $response);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $this->format_chat_response($response);
    }
    
    public function complete($args) {
        $defaults = [
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => '',
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'stop' => null
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        if (empty($args['prompt'])) {
            return new WP_Error('empty_prompt', __('Prompt cannot be empty', 'snn-ai-api-system'));
        }
        
        $data = [
            'model' => $args['model'],
            'prompt' => wp_kses_post($args['prompt']),
            'max_tokens' => intval($args['max_tokens']),
            'temperature' => floatval($args['temperature']),
            'top_p' => floatval($args['top_p']),
            'frequency_penalty' => floatval($args['frequency_penalty']),
            'presence_penalty' => floatval($args['presence_penalty'])
        ];
        
        if ($args['stop']) {
            $data['stop'] = $args['stop'];
        }
        
        $response = $this->make_request('/completions', 'POST', $data);
        
        $this->log_request('/completions', $data, $response);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $this->format_completion_response($response);
    }
    
    public function embed($text, $args = []) {
        $defaults = [
            'model' => 'text-embedding-3-small',
            'dimensions' => null
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        if (empty($text)) {
            return new WP_Error('empty_text', __('Text cannot be empty', 'snn-ai-api-system'));
        }
        
        $data = [
            'model' => $args['model'],
            'input' => sanitize_textarea_field($text)
        ];
        
        if ($args['dimensions']) {
            $data['dimensions'] = intval($args['dimensions']);
        }
        
        $response = $this->make_request('/embeddings', 'POST', $data);
        
        $this->log_request('/embeddings', $data, $response);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $this->format_embedding_response($response);
    }
    
    public function generate_image($args) {
        $defaults = [
            'model' => 'dall-e-3',
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
    
    protected function get_headers() {
        $headers = parent::get_headers();
        
        if (!empty($this->settings['organization'])) {
            $headers['OpenAI-Organization'] = $this->settings['organization'];
        }
        
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
            'id' => $response['id'] ?? null
        ];
    }
    
    private function format_completion_response($response) {
        $choice = $response['choices'][0] ?? null;
        
        if (!$choice) {
            return new WP_Error('invalid_response', __('Invalid response format', 'snn-ai-api-system'));
        }
        
        return [
            'text' => $choice['text'] ?? '',
            'finish_reason' => $choice['finish_reason'] ?? null,
            'usage' => $response['usage'] ?? null,
            'model' => $response['model'] ?? null,
            'created' => $response['created'] ?? null,
            'id' => $response['id'] ?? null
        ];
    }
    
    private function format_embedding_response($response) {
        $embedding = $response['data'][0] ?? null;
        
        if (!$embedding) {
            return new WP_Error('invalid_response', __('Invalid response format', 'snn-ai-api-system'));
        }
        
        return [
            'embedding' => $embedding['embedding'] ?? [],
            'index' => $embedding['index'] ?? 0,
            'usage' => $response['usage'] ?? null,
            'model' => $response['model'] ?? null
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
        
        $schema['organization'] = [
            'type' => 'string',
            'label' => __('Organization ID', 'snn-ai-api-system'),
            'description' => __('OpenAI organization ID (optional)', 'snn-ai-api-system'),
            'required' => false
        ];
        
        return $schema;
    }
}