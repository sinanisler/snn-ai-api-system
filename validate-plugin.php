<?php
/**
 * Plugin validation script
 */

// Simulate WordPress environment
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Mock WordPress functions
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://example.com/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('plugin_basename')) {
    function plugin_basename($file) {
        return basename(dirname($file)) . '/' . basename($file);
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability) {
        return true;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return strip_tags($str);
    }
}

if (!function_exists('get_transient')) {
    function get_transient($key) {
        return false;
    }
}

if (!function_exists('set_transient')) {
    function set_transient($key, $value, $expiration) {
        return true;
    }
}

if (!function_exists('delete_transient')) {
    function delete_transient($key) {
        return true;
    }
}

if (!function_exists('error_log')) {
    function error_log($message) {
        echo "LOG: " . $message . "\n";
    }
}

echo "=== SNN AI API System Plugin Validation ===\n\n";

try {
    // Test plugin file loading
    echo "1. Testing plugin file loading...\n";
    require_once 'snn-ai-api-system.php';
    echo "✓ Plugin file loaded successfully\n\n";
    
    // Test main class
    echo "2. Testing main class instantiation...\n";
    if (class_exists('SNN_AI_API_System')) {
        echo "✓ Main class exists\n";
        
        // Test singleton
        $instance = SNN_AI_API_System::get_instance();
        if ($instance instanceof SNN_AI_API_System) {
            echo "✓ Singleton works\n";
        } else {
            echo "✗ Singleton failed\n";
        }
        
        // Test method availability
        $methods = ['chat', 'complete', 'embed', 'generate_image', 'get_providers'];
        foreach ($methods as $method) {
            if (method_exists($instance, $method)) {
                echo "✓ Method '$method' exists\n";
            } else {
                echo "✗ Method '$method' missing\n";
            }
        }
        
    } else {
        echo "✗ Main class not found\n";
    }
    
    echo "\n3. Testing component classes...\n";
    $classes = [
        'SNN_AI_DB_Manager',
        'SNN_AI_Provider_Manager',
        'SNN_AI_Data_Injector',
        'SNN_AI_API_Manager',
        'SNN_AI_Admin_Manager',
        'SNN_AI_Security',
        'SNN_AI_Cache',
        'SNN_AI_Logger'
    ];
    
    foreach ($classes as $class) {
        if (class_exists($class)) {
            echo "✓ Class '$class' exists\n";
        } else {
            echo "✗ Class '$class' missing\n";
        }
    }
    
    echo "\n4. Testing helper functions...\n";
    $functions = ['snn_ai', 'snn_ai_chat', 'snn_ai_complete', 'snn_ai_embed', 'snn_ai_generate_image'];
    foreach ($functions as $function) {
        if (function_exists($function)) {
            echo "✓ Function '$function' exists\n";
        } else {
            echo "✗ Function '$function' missing\n";
        }
    }
    
    echo "\n=== Validation Complete ===\n";
    echo "Plugin structure appears to be valid!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "✗ Fatal Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}