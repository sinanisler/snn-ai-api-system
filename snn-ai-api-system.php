<?php
/**
 * Plugin Name: SNN AI API System
 * Plugin URI: https://github.com/sinanisler/snn-ai-api-system
 * Description: A comprehensive WordPress plugin for AI integration supporting multiple providers with powerful data injection capabilities.
 * Version: 1.0.0
 * Author: Sinan Isler
 * Author URI: https://sinanisler.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: snn-ai-api-system
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SNN_AI_API_SYSTEM_VERSION', '1.0.0');
define('SNN_AI_API_SYSTEM_PLUGIN_FILE', __FILE__);
define('SNN_AI_API_SYSTEM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SNN_AI_API_SYSTEM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SNN_AI_API_SYSTEM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Load required files
require_once SNN_AI_API_SYSTEM_PLUGIN_DIR . 'includes/class-snn-ai-api-system.php';

// Initialize the plugin
function snn_ai_api_system_init() {
    SNN_AI_API_System::get_instance();
}

// Hook into WordPress
add_action('plugins_loaded', 'snn_ai_api_system_init');

// Activation hook
register_activation_hook(__FILE__, 'snn_ai_api_system_activate');

// Deactivation hook
register_deactivation_hook(__FILE__, 'snn_ai_api_system_deactivate');

// Uninstall hook
register_uninstall_hook(__FILE__, 'snn_ai_api_system_uninstall');

// Hook callback functions
function snn_ai_api_system_activate() {
    require_once SNN_AI_API_SYSTEM_PLUGIN_DIR . 'includes/class-snn-ai-api-system.php';
    SNN_AI_API_System::activate();
}

function snn_ai_api_system_deactivate() {
    require_once SNN_AI_API_SYSTEM_PLUGIN_DIR . 'includes/class-snn-ai-api-system.php';
    SNN_AI_API_System::deactivate();
}

function snn_ai_api_system_uninstall() {
    require_once SNN_AI_API_SYSTEM_PLUGIN_DIR . 'includes/class-snn-ai-api-system.php';
    SNN_AI_API_System::uninstall();
}

// Global helper functions
function snn_ai() {
    return SNN_AI_API_System::get_instance();
}

function snn_ai_chat($args) {
    return snn_ai()->chat($args);
}

function snn_ai_complete($args) {
    return snn_ai()->complete($args);
}

function snn_ai_embed($text, $args = []) {
    return snn_ai()->embed($text, $args);
}

function snn_ai_generate_image($args) {
    return snn_ai()->generate_image($args);
}