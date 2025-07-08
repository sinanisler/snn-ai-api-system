<?php
/**
 * Abstract AI Provider
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class SNN_AI_Abstract_Provider implements SNN_AI_Provider_Interface {
    
    protected $name;
    protected $display_name;
    protected $api_key;
    protected $endpoint_url;
    protected $models = [];
    protected $settings = [];
    protected $default_model;
    protected $pricing = [];
    protected $capabilities = [];
    
    public function __construct($config = []) {
        $this->init_config($config);
        $this->init_models();
    }
    
    protected function init_config($config) {
        $this->api_key = $config['api_key'] ?? '';
        $this->endpoint_url = $config['endpoint_url'] ?? '';
        $this->settings = $config['settings'] ?? [];
    }
    
    protected function init_models() {
        // Override in child classes
    }
    
    public function get_models() {
        return $this->models;
    }
    
    public function test_connection() {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'No API key provided');
        }
        
        // Basic test - override in child classes
        return true;
    }
    
    protected function make_request($endpoint, $method = 'POST', $data = null) {
        $url = rtrim($this->endpoint_url, '/') . '/' . ltrim($endpoint, '/');
        
        $args = [
            'method' => $method,
            'timeout' => 30,
            'headers' => $this->get_headers(),
            'body' => $data ? json_encode($data) : null
        ];
        
        if (function_exists('wp_remote_request')) {
            $response = wp_remote_request($url, $args);
            
            if (is_wp_error($response)) {
                return $response;
            }
            
            $body = wp_remote_retrieve_body($response);
            $code = wp_remote_retrieve_response_code($response);
            
            if ($code >= 400) {
                return new WP_Error('api_error', "API Error: $code - $body");
            }
            
            return json_decode($body, true);
        }
        
        return new WP_Error('no_wp_remote', 'WordPress remote functions not available');
    }
    
    protected function get_headers() {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        ];
    }
    
    // Default implementations that can be overridden
    public function chat($args) {
        return new WP_Error('not_implemented', 'Chat not implemented for this provider');
    }
    
    public function complete($args) {
        return new WP_Error('not_implemented', 'Complete not implemented for this provider');
    }
    
    public function embed($text, $args = []) {
        return new WP_Error('not_implemented', 'Embed not implemented for this provider');
    }
    
    public function generate_image($args) {
        return new WP_Error('not_implemented', 'Image generation not implemented for this provider');
    }
}