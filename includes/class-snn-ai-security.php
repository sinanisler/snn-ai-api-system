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

    public function encrypt( $data ) {
        $key = $this->get_encryption_key();
        $iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
        $encrypted = openssl_encrypt( $data, 'aes-256-cbc', $key, 0, $iv );
        return base64_encode( $encrypted . '::' . $iv );
    }

    public function decrypt( $data ) {
        $key = $this->get_encryption_key();
        list( $encrypted_data, $iv ) = explode( '::', base64_decode( $data ), 2 );
        return openssl_decrypt( $encrypted_data, 'aes-256-cbc', $key, 0, $iv );
    }

    private function get_encryption_key() {
        $key = get_option( 'snn_ai_encryption_key' );
        if ( ! $key ) {
            $key = wp_generate_password( 32, true, true );
            update_option( 'snn_ai_encryption_key', $key );
        }
        return $key;
    }

    public function check_rate_limit() {
        $rate_limit = get_option( 'snn_ai_rate_limit', 100 );
        $rate_limit_period = get_option( 'snn_ai_rate_limit_period', 3600 );
        $user_id = get_current_user_id();

        if ( ! $user_id ) {
            return true; // Don't rate limit non-logged-in users for now
        }

        $transient_key = 'snn_ai_rate_limit_' . $user_id;
        $request_count = get_transient( $transient_key );

        if ( false === $request_count ) {
            set_transient( $transient_key, 1, $rate_limit_period );
        } elseif ( $request_count >= $rate_limit ) {
            return new WP_Error( 'rate_limit_exceeded', __( 'You have exceeded the rate limit.', 'snn-ai-api-system' ), [ 'status' => 429 ] );
        } else {
            set_transient( $transient_key, $request_count + 1, $rate_limit_period );
        }

        return true;
    }
}