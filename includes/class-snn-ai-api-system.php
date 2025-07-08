<?php
/**
 * Main plugin class for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_API_System {
    
    private static $instance = null;
    private $providers = [];
    private $data_injector = null;
    private $api_manager = null;
    private $admin_manager = null;
    private $db_manager = null;
    private $provider_manager = null;
    private $security = null;
    private $cache = null;
    private $logger = null;
    
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
        $this->init_components();
    }
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function init_hooks() {
        add_action('init', [$this, 'init']);
        add_action('rest_api_init', [$this, 'init_rest_api']);
        add_action('admin_init', [$this, 'init_admin']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }
    
    private function load_dependencies() {
        // Core classes - load only existing files
        $core_files = [
            'includes/class-snn-ai-db-manager.php',
            'includes/class-snn-ai-provider-manager.php', 
            'includes/class-snn-ai-data-injector.php',
            'includes/class-snn-ai-api-manager.php',
            'includes/class-snn-ai-admin-manager.php',
            'includes/class-snn-ai-security.php',
            'includes/class-snn-ai-cache.php',
            'includes/class-snn-ai-logger.php'
        ];
        
        foreach ($core_files as $file) {
            $filepath = SNN_AI_API_SYSTEM_PLUGIN_DIR . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            }
        }
        
        // Provider interface and abstract
        $provider_files = [
            'providers/interface-snn-ai-provider.php',
            'providers/abstract-snn-ai-provider.php'
        ];
        
        foreach ($provider_files as $file) {
            $filepath = SNN_AI_API_SYSTEM_PLUGIN_DIR . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            }
        }
        
        // Data injection classes
        $data_files = [
            'data-injection/class-snn-ai-query-builder.php',
            'data-injection/class-snn-ai-dynamic-tags.php'
        ];
        
        foreach ($data_files as $file) {
            $filepath = SNN_AI_API_SYSTEM_PLUGIN_DIR . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            }
        }
    }
    
    private function init_components() {
        // Initialize only existing classes
        if (class_exists('SNN_AI_DB_Manager')) {
            $this->db_manager = new SNN_AI_DB_Manager();
        }
        if (class_exists('SNN_AI_Provider_Manager')) {
            $this->provider_manager = new SNN_AI_Provider_Manager();
        }
        if (class_exists('SNN_AI_Data_Injector')) {
            $this->data_injector = new SNN_AI_Data_Injector();
        }
        if (class_exists('SNN_AI_API_Manager')) {
            $this->api_manager = new SNN_AI_API_Manager();
        }
        if (class_exists('SNN_AI_Admin_Manager')) {
            $this->admin_manager = new SNN_AI_Admin_Manager();
        }
        if (class_exists('SNN_AI_Security')) {
            $this->security = new SNN_AI_Security();
        }
        if (class_exists('SNN_AI_Cache')) {
            $this->cache = new SNN_AI_Cache();
        }
        if (class_exists('SNN_AI_Logger')) {
            $this->logger = new SNN_AI_Logger();
        }
    }
    
    public function init() {
        // Initialize text domain
        load_plugin_textdomain('snn-ai-api-system', false, dirname(SNN_AI_API_SYSTEM_PLUGIN_BASENAME) . '/languages');
        
        // Initialize components that exist
        if ($this->provider_manager && method_exists($this->provider_manager, 'init')) {
            $this->provider_manager->init();
        }
        if ($this->data_injector && method_exists($this->data_injector, 'init')) {
            $this->data_injector->init();
        }
        
        // Fire init action
        do_action('snn_ai_init', $this);
    }
    
    public function init_rest_api() {
        if ($this->api_manager && method_exists($this->api_manager, 'init_rest_api')) {
            $this->api_manager->init_rest_api();
        }
    }
    
    public function init_admin() {
        if (is_admin() && $this->admin_manager && method_exists($this->admin_manager, 'init')) {
            $this->admin_manager->init();
        }
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script(
            'snn-ai-api-system',
            SNN_AI_API_SYSTEM_PLUGIN_URL . 'assets/js/snn-ai-api-system.js',
            ['wp-api-fetch'],
            SNN_AI_API_SYSTEM_VERSION,
            true
        );
        
        wp_localize_script('snn-ai-api-system', 'snnAI', [
            'apiUrl' => rest_url('snn-ai/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'version' => SNN_AI_API_SYSTEM_VERSION
        ]);
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'snn-ai') === false) {
            return;
        }
        
        wp_enqueue_script(
            'snn-ai-admin',
            SNN_AI_API_SYSTEM_PLUGIN_URL . 'assets/js/snn-ai-admin.js',
            ['wp-api-fetch', 'wp-components', 'wp-element'],
            SNN_AI_API_SYSTEM_VERSION,
            true
        );
        
        wp_enqueue_style(
            'snn-ai-admin',
            SNN_AI_API_SYSTEM_PLUGIN_URL . 'assets/css/snn-ai-admin.css',
            [],
            SNN_AI_API_SYSTEM_VERSION
        );
    }
    
    // Core API methods
    public function chat($args) {
        if ($this->provider_manager && method_exists($this->provider_manager, 'chat')) {
            return $this->provider_manager->chat($args);
        }
        return new WP_Error('provider_unavailable', 'Provider manager not available');
    }
    
    public function complete($args) {
        if ($this->provider_manager && method_exists($this->provider_manager, 'complete')) {
            return $this->provider_manager->complete($args);
        }
        return new WP_Error('provider_unavailable', 'Provider manager not available');
    }
    
    public function embed($text, $args = []) {
        if ($this->provider_manager && method_exists($this->provider_manager, 'embed')) {
            return $this->provider_manager->embed($text, $args);
        }
        return new WP_Error('provider_unavailable', 'Provider manager not available');
    }
    
    public function generate_image($args) {
        if ($this->provider_manager && method_exists($this->provider_manager, 'generate_image')) {
            return $this->provider_manager->generate_image($args);
        }
        return new WP_Error('provider_unavailable', 'Provider manager not available');
    }
    
    // Provider management
    public function get_providers() {
        if ($this->provider_manager && method_exists($this->provider_manager, 'get_providers')) {
            return $this->provider_manager->get_providers();
        }
        return [];
    }
    
    public function get_provider($name) {
        if ($this->provider_manager && method_exists($this->provider_manager, 'get_provider')) {
            return $this->provider_manager->get_provider($name);
        }
        return null;
    }
    
    public function register_provider($name, $class) {
        if ($this->provider_manager && method_exists($this->provider_manager, 'register_provider')) {
            return $this->provider_manager->register_provider($name, $class);
        }
        return false;
    }
    
    // Data injection
    public function inject_data($template_id, $context = []) {
        if ($this->data_injector && method_exists($this->data_injector, 'inject_data')) {
            return $this->data_injector->inject_data($template_id, $context);
        }
        return ['content' => '', 'data' => []];
    }
    
    public function create_data_template($name, $config) {
        if ($this->data_injector && method_exists($this->data_injector, 'create_template')) {
            return $this->data_injector->create_template($name, $config);
        }
        return false;
    }
    
    // Database methods
    public function get_db_manager() {
        return $this->db_manager;
    }
    
    // Security methods
    public function verify_permissions($capability = 'manage_options') {
        if ($this->security && method_exists($this->security, 'verify_permissions')) {
            return $this->security->verify_permissions($capability);
        }
        return current_user_can($capability);
    }
    
    public function sanitize_input($input, $type = 'text') {
        if ($this->security && method_exists($this->security, 'sanitize_input')) {
            return $this->security->sanitize_input($input, $type);
        }
        return sanitize_text_field($input);
    }
    
    // Cache methods
    public function get_cache($key) {
        if ($this->cache && method_exists($this->cache, 'get')) {
            return $this->cache->get($key);
        }
        return false;
    }
    
    public function set_cache($key, $value, $expiration = 3600) {
        if ($this->cache && method_exists($this->cache, 'set')) {
            return $this->cache->set($key, $value, $expiration);
        }
        return false;
    }
    
    // Logging methods
    public function log($message, $level = 'info', $context = []) {
        if ($this->logger && method_exists($this->logger, 'log')) {
            return $this->logger->log($message, $level, $context);
        }
        return false;
    }
    
    // Plugin activation
    public static function activate() {
        // Load DB manager for activation
        if (!class_exists('SNN_AI_DB_Manager')) {
            require_once SNN_AI_API_SYSTEM_PLUGIN_DIR . 'includes/class-snn-ai-db-manager.php';
        }
        
        $db_manager = new SNN_AI_DB_Manager();
        $db_manager->create_tables();
        $db_manager->create_default_data();
        
        // Set version
        update_option('snn_ai_api_system_version', SNN_AI_API_SYSTEM_VERSION);
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        do_action('snn_ai_activated');
    }
    
    // Plugin deactivation
    public static function deactivate() {
        // Clean up scheduled events
        wp_clear_scheduled_hook('snn_ai_cleanup');
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        do_action('snn_ai_deactivated');
    }
    
    // Plugin uninstall
    public static function uninstall() {
        if (!current_user_can('activate_plugins')) {
            return;
        }
        
        // Load DB manager for uninstall
        if (!class_exists('SNN_AI_DB_Manager')) {
            require_once SNN_AI_API_SYSTEM_PLUGIN_DIR . 'includes/class-snn-ai-db-manager.php';
        }
        
        // Remove database tables
        $db_manager = new SNN_AI_DB_Manager();
        $db_manager->drop_tables();
        
        // Remove options
        delete_option('snn_ai_api_system_version');
        delete_option('snn_ai_providers');
        delete_option('snn_ai_settings');
        
        // Remove user meta
        delete_metadata('user', 0, 'snn_ai_preferences', '', true);
        
        do_action('snn_ai_uninstalled');
    }
    
    // Version check and upgrade
    public function check_version() {
        $current_version = get_option('snn_ai_api_system_version', '0.0.0');
        
        if (version_compare($current_version, SNN_AI_API_SYSTEM_VERSION, '<')) {
            $this->upgrade($current_version);
        }
    }
    
    private function upgrade($from_version) {
        // Run upgrade routines
        if (version_compare($from_version, '1.0.0', '<')) {
            $this->upgrade_to_1_0_0();
        }
        
        // Update version
        update_option('snn_ai_api_system_version', SNN_AI_API_SYSTEM_VERSION);
        
        do_action('snn_ai_upgraded', $from_version, SNN_AI_API_SYSTEM_VERSION);
    }
    
    private function upgrade_to_1_0_0() {
        // Initial upgrade routines
        if ($this->db_manager && method_exists($this->db_manager, 'create_tables')) {
            $this->db_manager->create_tables();
        }
    }
}