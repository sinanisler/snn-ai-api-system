<?php
/**
 * Security Manager for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Security {
    
    public function __construct() {
        // Initialize
    }
    
    public function verify_permissions($capability = 'manage_options') {
        return current_user_can($capability);
    }
    
    public function sanitize_input($input, $type = 'text') {
        switch ($type) {
            case 'text':
                return sanitize_text_field($input);
            case 'textarea':
                return sanitize_textarea_field($input);
            case 'email':
                return sanitize_email($input);
            case 'url':
                return esc_url_raw($input);
            default:
                return sanitize_text_field($input);
        }
    }
}