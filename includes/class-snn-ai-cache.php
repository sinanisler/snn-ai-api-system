<?php
/**
 * Cache Manager for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Cache {
    
    public function __construct() {
        // Initialize
    }
    
    public function get($key) {
        return get_transient('snn_ai_' . $key);
    }
    
    public function set($key, $value, $expiration = 3600) {
        return set_transient('snn_ai_' . $key, $value, $expiration);
    }
    
    public function delete($key) {
        return delete_transient('snn_ai_' . $key);
    }
}