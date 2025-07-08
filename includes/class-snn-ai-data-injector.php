<?php
/**
 * Data Injector for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Data_Injector {
    
    public function __construct() {
        // Initialize
    }
    
    public function init() {
        // Load data sources
    }
    
    public function inject_data( $template_id, $context = [] ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'snn_ai_templates';

        $template = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $template_id ), ARRAY_A );

        if ( ! $template ) {
            return new WP_Error( 'template_not_found', __( 'Template not found.', 'snn-ai-api-system' ) );
        }

        $config = json_decode( $template['config'], true );
        $query_builder = new SNN_AI_Query_Builder();
        $query = $query_builder->build( $config['query_config'] );
        $posts = $query->get_posts();

        $data = [];
        foreach ( $posts as $post ) {
            setup_postdata( $post );
            $item = [];
            foreach ( $config['dynamic_tags'] as $key => $tag ) {
                $item[ $key ] = $this->process_dynamic_tag( $tag, $post );
            }
            $data[] = $item;
        }
        wp_reset_postdata();

        $content = $this->render_template( $config['template_content'], [ 'posts' => $data ] );

        return [
            'content' => $content,
            'data'    => $data,
        ];
    }

    private function process_dynamic_tag( $tag, $post ) {
        if ( function_exists( $tag ) ) {
            return call_user_func( $tag, $post->ID );
        }
        return '';
    }

    private function render_template( $template_content, $data ) {
        // A simple regex-based template engine
        $content = preg_replace_callback( '/{{\#posts}}(.*?){{\/posts}}/s', function( $matches ) use ( $data ) {
            $item_template = $matches[1];
            $output = '';
            foreach ( $data['posts'] as $item ) {
                $item_output = $item_template;
                foreach ( $item as $key => $value ) {
                    $item_output = str_replace( '{{{' . $key . '}}}', $value, $item_output );
                }
                $output .= $item_output;
            }
            return $output;
        }, $template_content );

        return $content;
    }
    
    public function create_template( $name, $config ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'snn_ai_templates';

        $data = [
            'name'        => sanitize_text_field( $name ),
            'config'      => wp_json_encode( $config ),
            'created_at'  => current_time( 'mysql' ),
            'updated_at'  => current_time( 'mysql' ),
        ];

        $format = [
            '%s',
            '%s',
            '%s',
            '%s',
        ];

        $wpdb->insert( $table_name, $data, $format );

        return $wpdb->insert_id;
    }
}