<?php
/**
 * Logger for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Logger {
    
    public function __construct() {
        // Initialize
    }
    
    public function log($message, $level = 'info', $context = []) {
        // Use WordPress error log
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("SNN AI [{$level}]: {$message}");
        }
        
        // Also log to database if available
        if (class_exists('SNN_AI_DB_Manager')) {
            $db_manager = new SNN_AI_DB_Manager();
            $db_manager->log($message, $level, $context);
        }
    }
}