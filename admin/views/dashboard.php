<?php
/**
 * Dashboard admin view
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="snn-ai-dashboard">
        <div class="welcome-panel">
            <div class="welcome-panel-content">
                <h2><?php _e('Welcome to SNN AI API System', 'snn-ai-api-system'); ?></h2>
                <p><?php _e('A comprehensive WordPress plugin for AI integration supporting multiple providers with powerful data injection capabilities.', 'snn-ai-api-system'); ?></p>
                
                <div class="welcome-panel-column-container">
                    <div class="welcome-panel-column">
                        <h3><?php _e('Get Started', 'snn-ai-api-system'); ?></h3>
                        <p><?php _e('Configure your AI providers and start using AI features.', 'snn-ai-api-system'); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=snn-ai-providers'); ?>" class="button button-primary"><?php _e('Configure Providers', 'snn-ai-api-system'); ?></a>
                    </div>
                    
                    <div class="welcome-panel-column">
                        <h3><?php _e('Create Templates', 'snn-ai-api-system'); ?></h3>
                        <p><?php _e('Build data injection templates to enhance your AI interactions.', 'snn-ai-api-system'); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=snn-ai-templates'); ?>" class="button"><?php _e('Manage Templates', 'snn-ai-api-system'); ?></a>
                    </div>
                    
                    <div class="welcome-panel-column">
                        <h3><?php _e('Test Chat', 'snn-ai-api-system'); ?></h3>
                        <p><?php _e('Try out the AI chat interface with your configured providers.', 'snn-ai-api-system'); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=snn-ai-chat'); ?>" class="button"><?php _e('Open Chat', 'snn-ai-api-system'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="metabox-holder">
            <div class="postbox-container" style="width: 50%;">
                <div class="postbox">
                    <h2 class="hndle"><?php _e('Active Providers', 'snn-ai-api-system'); ?></h2>
                    <div class="inside">
                        <?php if (empty($active_providers)): ?>
                            <p><?php _e('No active providers configured.', 'snn-ai-api-system'); ?></p>
                            <a href="<?php echo admin_url('admin.php?page=snn-ai-providers'); ?>" class="button"><?php _e('Configure Providers', 'snn-ai-api-system'); ?></a>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($active_providers as $name => $provider): ?>
                                    <li>
                                        <strong><?php echo esc_html($provider->get_display_name()); ?></strong><br>
                                        <?php echo esc_html($provider->get_description()); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="postbox-container" style="width: 50%;">
                <div class="postbox">
                    <h2 class="hndle"><?php _e('Usage Statistics', 'snn-ai-api-system'); ?></h2>
                    <div class="inside">
                        <?php if (empty($usage_stats)): ?>
                            <p><?php _e('No usage data available yet.', 'snn-ai-api-system'); ?></p>
                        <?php else: ?>
                            <table class="wp-list-table widefat fixed striped">
                                <thead>
                                    <tr>
                                        <th><?php _e('Provider', 'snn-ai-api-system'); ?></th>
                                        <th><?php _e('Requests', 'snn-ai-api-system'); ?></th>
                                        <th><?php _e('Tokens', 'snn-ai-api-system'); ?></th>
                                        <th><?php _e('Cost', 'snn-ai-api-system'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usage_stats as $stat): ?>
                                        <tr>
                                            <td><?php echo esc_html($stat['provider']); ?></td>
                                            <td><?php echo intval($stat['requests']); ?></td>
                                            <td><?php echo number_format(intval($stat['total_tokens'])); ?></td>
                                            <td>$<?php echo number_format(floatval($stat['total_cost']), 4); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.snn-ai-dashboard .welcome-panel {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 20px;
}

.snn-ai-dashboard .welcome-panel-column-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.snn-ai-dashboard .welcome-panel-column {
    flex: 1;
}

.snn-ai-dashboard .postbox-container {
    float: left;
    padding: 0 10px;
}

.snn-ai-dashboard .postbox {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin-bottom: 20px;
}

.snn-ai-dashboard .postbox h2.hndle {
    background: #f9f9f9;
    border-bottom: 1px solid #c3c4c7;
    margin: 0;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
}

.snn-ai-dashboard .postbox .inside {
    padding: 20px;
}
</style>