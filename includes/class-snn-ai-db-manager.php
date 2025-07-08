<?php
/**
 * Database Manager for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_DB_Manager {
    
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Create database tables
     */
    public function create_tables() {
        $charset_collate = $this->wpdb->get_charset_collate();
        
        // Providers table
        $providers_table = $this->wpdb->prefix . 'snn_ai_providers';
        $providers_sql = "CREATE TABLE $providers_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            display_name varchar(200) NOT NULL,
            type varchar(50) NOT NULL,
            api_key text NOT NULL,
            endpoint_url varchar(500) DEFAULT '',
            organization varchar(200) DEFAULT '',
            settings longtext DEFAULT '',
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY name (name)
        ) $charset_collate;";
        
        // Sessions table
        $sessions_table = $this->wpdb->prefix . 'snn_ai_sessions';
        $sessions_sql = "CREATE TABLE $sessions_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            provider varchar(100) NOT NULL,
            model varchar(100) NOT NULL,
            messages longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY provider (provider)
        ) $charset_collate;";
        
        // Templates table
        $templates_table = $this->wpdb->prefix . 'snn_ai_templates';
        $templates_sql = "CREATE TABLE $templates_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(200) NOT NULL,
            description text DEFAULT '',
            query_config longtext NOT NULL,
            dynamic_tags longtext NOT NULL,
            template_content longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Logs table
        $logs_table = $this->wpdb->prefix . 'snn_ai_logs';
        $logs_sql = "CREATE TABLE $logs_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT 0,
            provider varchar(100) DEFAULT '',
            model varchar(100) DEFAULT '',
            action varchar(100) NOT NULL,
            message text NOT NULL,
            context longtext DEFAULT '',
            level varchar(20) DEFAULT 'info',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY provider (provider),
            KEY level (level),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Cache table
        $cache_table = $this->wpdb->prefix . 'snn_ai_cache';
        $cache_sql = "CREATE TABLE $cache_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            cache_key varchar(255) NOT NULL,
            cache_value longtext NOT NULL,
            expiration datetime NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY cache_key (cache_key),
            KEY expiration (expiration)
        ) $charset_collate;";
        
        // Usage table
        $usage_table = $this->wpdb->prefix . 'snn_ai_usage';
        $usage_sql = "CREATE TABLE $usage_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            provider varchar(100) NOT NULL,
            model varchar(100) NOT NULL,
            action varchar(100) NOT NULL,
            tokens_used int(11) DEFAULT 0,
            cost decimal(10,4) DEFAULT 0.0000,
            response_time decimal(8,3) DEFAULT 0.000,
            status varchar(20) DEFAULT 'success',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY provider (provider),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($providers_sql);
        dbDelta($sessions_sql);
        dbDelta($templates_sql);
        dbDelta($logs_sql);
        dbDelta($cache_sql);
        dbDelta($usage_sql);
    }
    
    /**
     * Create default data
     */
    public function create_default_data() {
        // Create default providers
        $this->create_default_providers();
    }
    
    /**
     * Create default providers
     */
    private function create_default_providers() {
        $providers = [
            [
                'name' => 'openai',
                'display_name' => 'OpenAI',
                'type' => 'openai',
                'api_key' => '',
                'endpoint_url' => 'https://api.openai.com/v1',
                'is_active' => 0
            ],
            [
                'name' => 'openrouter',
                'display_name' => 'OpenRouter',
                'type' => 'openrouter',
                'api_key' => '',
                'endpoint_url' => 'https://openrouter.ai/api/v1',
                'is_active' => 0
            ],
            [
                'name' => 'anthropic',
                'display_name' => 'Anthropic',
                'type' => 'anthropic',
                'api_key' => '',
                'endpoint_url' => 'https://api.anthropic.com/v1',
                'is_active' => 0
            ]
        ];
        
        $providers_table = $this->wpdb->prefix . 'snn_ai_providers';
        
        foreach ($providers as $provider) {
            $existing = $this->wpdb->get_row($this->wpdb->prepare(
                "SELECT id FROM $providers_table WHERE name = %s",
                $provider['name']
            ));
            
            if (!$existing) {
                $this->wpdb->insert($providers_table, $provider);
            }
        }
    }
    
    /**
     * Drop all tables
     */
    public function drop_tables() {
        $tables = [
            'snn_ai_providers',
            'snn_ai_sessions',
            'snn_ai_templates',
            'snn_ai_logs',
            'snn_ai_cache',
            'snn_ai_usage'
        ];
        
        foreach ($tables as $table) {
            $table_name = $this->wpdb->prefix . $table;
            $this->wpdb->query("DROP TABLE IF EXISTS $table_name");
        }
    }
    
    /**
     * Get provider by name
     */
    public function get_provider($name) {
        $providers_table = $this->wpdb->prefix . 'snn_ai_providers';
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM $providers_table WHERE name = %s",
            $name
        ));
    }
    
    /**
     * Get all providers
     */
    public function get_providers() {
        $providers_table = $this->wpdb->prefix . 'snn_ai_providers';
        return $this->wpdb->get_results("SELECT * FROM $providers_table ORDER BY name");
    }
    
    /**
     * Update provider
     */
    public function update_provider($name, $data) {
        $providers_table = $this->wpdb->prefix . 'snn_ai_providers';
        return $this->wpdb->update(
            $providers_table,
            $data,
            ['name' => $name]
        );
    }
    
    /**
     * Log message
     */
    public function log($message, $level = 'info', $context = []) {
        $logs_table = $this->wpdb->prefix . 'snn_ai_logs';
        
        $data = [
            'user_id' => get_current_user_id(),
            'message' => $message,
            'level' => $level,
            'context' => json_encode($context)
        ];
        
        if (isset($context['provider'])) {
            $data['provider'] = $context['provider'];
        }
        if (isset($context['model'])) {
            $data['model'] = $context['model'];
        }
        if (isset($context['action'])) {
            $data['action'] = $context['action'];
        }
        
        return $this->wpdb->insert($logs_table, $data);
    }
    
    /**
     * Get template by ID
     */
    public function get_template($id) {
        $templates_table = $this->wpdb->prefix . 'snn_ai_templates';
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM $templates_table WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Create template
     */
    public function create_template($name, $config) {
        $templates_table = $this->wpdb->prefix . 'snn_ai_templates';
        
        $data = [
            'name' => $name,
            'description' => $config['description'] ?? '',
            'query_config' => json_encode($config['query_config'] ?? []),
            'dynamic_tags' => json_encode($config['dynamic_tags'] ?? []),
            'template_content' => $config['template_content'] ?? ''
        ];
        
        $result = $this->wpdb->insert($templates_table, $data);
        
        if ($result) {
            return $this->wpdb->insert_id;
        }
        
        return false;
    }
}