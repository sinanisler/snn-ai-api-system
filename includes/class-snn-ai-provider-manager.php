<?php
/**
 * Provider Manager for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Provider_Manager {
    
    private $providers = [];
    
    public function __construct() {
        // Initialize
    }
    
    public function init() {
        // Load available providers
        $this->load_providers();
    }
    
    private function load_providers() {
        $provider_dirs = glob( SNN_AI_API_SYSTEM_PLUGIN_DIR . 'providers/*', GLOB_ONLYDIR );

        foreach ( $provider_dirs as $dir ) {
            $provider_name = basename( $dir );
            $file = $dir . '/class-snn-ai-' . $provider_name . '-provider.php';
            if ( file_exists( $file ) ) {
                require_once $file;
                $class_name = 'SNN_AI_' . str_replace( '-', '_', ucwords( $provider_name, '-' ) ) . '_Provider';
                if ( class_exists( $class_name ) ) {
                    $this->providers[ $provider_name ] = new $class_name();
                }
            }
        }
    }

    public function get_active_providers() {
        $active_providers = [];
        foreach ( $this->providers as $name => $provider ) {
            if ( $provider->is_active() ) {
                $active_providers[ $name ] = $provider;
            }
        }
        return $active_providers;
    }
    
    public function get_providers() {
        return $this->providers;
    }
    
    public function get_provider($name) {
        return $this->providers[$name] ?? null;
    }
    
    public function register_provider($name, $provider) {
        $this->providers[$name] = $provider;
    }
    
    public function chat($args) {
        $provider_name = $args['provider'] ?? '';
        $provider = $this->get_provider($provider_name);
        
        if (!$provider) {
            return new WP_Error('invalid_provider', 'Invalid provider specified');
        }
        
        return $provider->chat($args);
    }
    
    public function complete($args) {
        $provider_name = $args['provider'] ?? '';
        $provider = $this->get_provider($provider_name);
        
        if (!$provider) {
            return new WP_Error('invalid_provider', 'Invalid provider specified');
        }
        
        return $provider->complete($args);
    }
    
    public function embed($text, $args = []) {
        $provider_name = $args['provider'] ?? '';
        $provider = $this->get_provider($provider_name);
        
        if (!$provider) {
            return new WP_Error('invalid_provider', 'Invalid provider specified');
        }
        
        return $provider->embed($text, $args);
    }
    
    public function generate_image($args) {
        $provider_name = $args['provider'] ?? '';
        $provider = $this->get_provider($provider_name);
        
        if (!$provider) {
            return new WP_Error('invalid_provider', 'Invalid provider specified');
        }
        
        return $provider->generate_image($args);
    }
}