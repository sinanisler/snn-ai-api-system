<?php
/**
 * AI Provider Interface
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

interface SNN_AI_Provider_Interface {
    
    /**
     * Chat completion
     */
    public function chat($args);
    
    /**
     * Text completion
     */
    public function complete($args);
    
    /**
     * Generate embeddings
     */
    public function embed($text, $args = []);
    
    /**
     * Generate images
     */
    public function generate_image($args);
    
    /**
     * Get available models
     */
    public function get_models();
    
    /**
     * Test connection
     */
    public function test_connection();
}