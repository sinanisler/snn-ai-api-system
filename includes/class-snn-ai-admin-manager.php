<?php
/**
 * Admin Manager for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Admin_Manager {
    
    private $plugin_name;
    private $version;
    
    public function __construct() {
        $this->plugin_name = 'snn-ai-api-system';
        $this->version = SNN_AI_API_SYSTEM_VERSION;
    }
    
    public function init() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'init_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }
    
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            'SNN AI API System',
            'SNN AI API',
            'manage_options',
            'snn-ai-dashboard',
            [$this, 'dashboard_page'],
            'dashicons-admin-generic',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'snn-ai-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'snn-ai-dashboard',
            [$this, 'dashboard_page']
        );
        
        // Providers submenu
        add_submenu_page(
            'snn-ai-dashboard',
            'AI Providers',
            'Providers',
            'manage_options',
            'snn-ai-providers',
            [$this, 'providers_page']
        );
        
        // Templates submenu
        add_submenu_page(
            'snn-ai-dashboard',
            'Templates',
            'Templates',
            'manage_options',
            'snn-ai-templates',
            [$this, 'templates_page']
        );
        
        // Settings submenu
        add_submenu_page(
            'snn-ai-dashboard',
            'Settings',
            'Settings',
            'manage_options',
            'snn-ai-settings',
            [$this, 'settings_page']
        );
        
        // Logs submenu
        add_submenu_page(
            'snn-ai-dashboard',
            'Logs',
            'Logs',
            'manage_options',
            'snn-ai-logs',
            [$this, 'logs_page']
        );
    }
    
    public function init_settings() {
        register_setting('snn_ai_settings', 'snn_ai_providers');
        register_setting('snn_ai_settings', 'snn_ai_general_settings');
        
        // General settings section
        add_settings_section(
            'snn_ai_general_section',
            'General Settings',
            [$this, 'general_section_callback'],
            'snn-ai-settings'
        );
        
        // Provider settings sections
        add_settings_section(
            'snn_ai_providers_section',
            'AI Provider Settings',
            [$this, 'providers_section_callback'],
            'snn-ai-settings'
        );
        
        // Add fields
        add_settings_field(
            'enable_logging',
            'Enable Logging',
            [$this, 'enable_logging_callback'],
            'snn-ai-settings',
            'snn_ai_general_section'
        );
        
        add_settings_field(
            'cache_duration',
            'Cache Duration (seconds)',
            [$this, 'cache_duration_callback'],
            'snn-ai-settings',
            'snn_ai_general_section'
        );
        
        add_settings_field(
            'openai_api_key',
            'OpenAI API Key',
            [$this, 'openai_api_key_callback'],
            'snn-ai-settings',
            'snn_ai_providers_section'
        );
        
        add_settings_field(
            'anthropic_api_key',
            'Anthropic API Key',
            [$this, 'anthropic_api_key_callback'],
            'snn-ai-settings',
            'snn_ai_providers_section'
        );
        
        add_settings_field(
            'openrouter_api_key',
            'OpenRouter API Key',
            [$this, 'openrouter_api_key_callback'],
            'snn-ai-settings',
            'snn_ai_providers_section'
        );
    }
    
    public function dashboard_page() {
        ?>
        <div class="wrap">
            <h1>SNN AI API System Dashboard</h1>
            <div class="snn-ai-dashboard">
                <div class="snn-ai-cards">
                    <div class="snn-ai-card">
                        <h3>System Status</h3>
                        <p>Plugin Version: <?php echo $this->version; ?></p>
                        <p>Status: <span class="status-active">Active</span></p>
                    </div>
                    
                    <div class="snn-ai-card">
                        <h3>Providers</h3>
                        <?php
                        $providers = get_option('snn_ai_providers', []);
                        $active_providers = array_filter($providers, function($provider) {
                            return !empty($provider['api_key']);
                        });
                        ?>
                        <p>Active Providers: <?php echo count($active_providers); ?></p>
                        <p><a href="<?php echo admin_url('admin.php?page=snn-ai-providers'); ?>">Manage Providers</a></p>
                    </div>
                    
                    <div class="snn-ai-card">
                        <h3>Recent Activity</h3>
                        <p>API Calls Today: 0</p>
                        <p>Last Call: Never</p>
                        <p><a href="<?php echo admin_url('admin.php?page=snn-ai-logs'); ?>">View Logs</a></p>
                    </div>
                </div>
                
                <div class="snn-ai-quick-actions">
                    <h3>Quick Actions</h3>
                    <a href="<?php echo admin_url('admin.php?page=snn-ai-providers'); ?>" class="button button-primary">Configure Providers</a>
                    <a href="<?php echo admin_url('admin.php?page=snn-ai-templates'); ?>" class="button">Manage Templates</a>
                    <a href="<?php echo admin_url('admin.php?page=snn-ai-settings'); ?>" class="button">Settings</a>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function providers_page() {
        ?>
        <div class="wrap">
            <h1>AI Providers</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('snn_ai_settings');
                $providers = get_option('snn_ai_providers', []);
                ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">OpenAI</th>
                        <td>
                            <input type="text" name="snn_ai_providers[openai][api_key]" value="<?php echo esc_attr($providers['openai']['api_key'] ?? ''); ?>" placeholder="sk-..." class="regular-text" />
                            <p class="description">Enter your OpenAI API key</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Anthropic</th>
                        <td>
                            <input type="text" name="snn_ai_providers[anthropic][api_key]" value="<?php echo esc_attr($providers['anthropic']['api_key'] ?? ''); ?>" placeholder="sk-ant-..." class="regular-text" />
                            <p class="description">Enter your Anthropic API key</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">OpenRouter</th>
                        <td>
                            <input type="text" name="snn_ai_providers[openrouter][api_key]" value="<?php echo esc_attr($providers['openrouter']['api_key'] ?? ''); ?>" placeholder="sk-or-..." class="regular-text" />
                            <p class="description">Enter your OpenRouter API key</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    public function templates_page() {
        ?>
        <div class="wrap">
            <h1>Templates</h1>
            <p>Manage your AI prompt templates and data injection configurations.</p>
            <div class="snn-ai-templates">
                <p><em>Template management functionality coming soon...</em></p>
            </div>
        </div>
        <?php
    }
    
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('snn_ai_settings');
                do_settings_sections('snn-ai-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    public function logs_page() {
        ?>
        <div class="wrap">
            <h1>Logs</h1>
            <p>View system logs and API call history.</p>
            <div class="snn-ai-logs">
                <p><em>Log viewing functionality coming soon...</em></p>
            </div>
        </div>
        <?php
    }
    
    // Settings callbacks
    public function general_section_callback() {
        echo '<p>Configure general plugin settings.</p>';
    }
    
    public function providers_section_callback() {
        echo '<p>Configure your AI provider API keys.</p>';
    }
    
    public function enable_logging_callback() {
        $settings = get_option('snn_ai_general_settings', []);
        $value = $settings['enable_logging'] ?? 1;
        echo '<input type="checkbox" name="snn_ai_general_settings[enable_logging]" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function cache_duration_callback() {
        $settings = get_option('snn_ai_general_settings', []);
        $value = $settings['cache_duration'] ?? 3600;
        echo '<input type="number" name="snn_ai_general_settings[cache_duration]" value="' . esc_attr($value) . '" min="60" max="86400" />';
    }
    
    public function openai_api_key_callback() {
        $providers = get_option('snn_ai_providers', []);
        $value = $providers['openai']['api_key'] ?? '';
        echo '<input type="text" name="snn_ai_providers[openai][api_key]" value="' . esc_attr($value) . '" class="regular-text" placeholder="sk-..." />';
    }
    
    public function anthropic_api_key_callback() {
        $providers = get_option('snn_ai_providers', []);
        $value = $providers['anthropic']['api_key'] ?? '';
        echo '<input type="text" name="snn_ai_providers[anthropic][api_key]" value="' . esc_attr($value) . '" class="regular-text" placeholder="sk-ant-..." />';
    }
    
    public function openrouter_api_key_callback() {
        $providers = get_option('snn_ai_providers', []);
        $value = $providers['openrouter']['api_key'] ?? '';
        echo '<input type="text" name="snn_ai_providers[openrouter][api_key]" value="' . esc_attr($value) . '" class="regular-text" placeholder="sk-or-..." />';
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'snn-ai') === false) {
            return;
        }
        
        wp_enqueue_style('snn-ai-admin-style', SNN_AI_API_SYSTEM_PLUGIN_URL . 'assets/css/admin.css', [], $this->version);
    }
}