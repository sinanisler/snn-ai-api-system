<?php
/**
 * API Manager for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_API_Manager {
    
    public function __construct() {
        // Initialize
    }
    
    public function init_rest_api() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        $namespace = 'snn-ai/v1';

        register_rest_route( $namespace, '/chat', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'handle_chat_request' ],
            'permission_callback' => [ $this, 'check_permissions' ],
        ] );

        register_rest_route( $namespace, '/complete', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'handle_complete_request' ],
            'permission_callback' => [ $this, 'check_permissions' ],
        ] );

        register_rest_route( $namespace, '/embed', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'handle_embed_request' ],
            'permission_callback' => [ $this, 'check_permissions' ],
        ] );

        register_rest_route( $namespace, '/generate-image', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'handle_generate_image_request' ],
            'permission_callback' => [ $this, 'check_permissions' ],
        ] );
    }

    public function handle_chat_request( WP_REST_Request $request ) {
        $security = SNN_AI_API_System::get_instance()->get_security();
        $rate_limit_check = $security->check_rate_limit();
        if ( is_wp_error( $rate_limit_check ) ) {
            return $rate_limit_check;
        }
        $provider_manager = SNN_AI_API_System::get_instance()->get_provider_manager();
        $provider_name = $request->get_param( 'provider' );
        $provider = $provider_manager->get_provider( $provider_name );

        if ( ! $provider ) {
            return new WP_Error( 'invalid_provider', __( 'Invalid provider specified.', 'snn-ai-api-system' ), [ 'status' => 400 ] );
        }

        $args = $request->get_json_params();
        $response = $provider->chat( $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return new WP_REST_Response( $response, 200 );
    }

    public function handle_complete_request( WP_REST_Request $request ) {
        $security = SNN_AI_API_System::get_instance()->get_security();
        $rate_limit_check = $security->check_rate_limit();
        if ( is_wp_error( $rate_limit_check ) ) {
            return $rate_limit_check;
        }
        $provider_manager = SNN_AI_API_System::get_instance()->get_provider_manager();
        $provider_name = $request->get_param( 'provider' );
        $provider = $provider_manager->get_provider( $provider_name );

        if ( ! $provider ) {
            return new WP_Error( 'invalid_provider', __( 'Invalid provider specified.', 'snn-ai-api-system' ), [ 'status' => 400 ] );
        }

        $args = $request->get_json_params();
        $response = $provider->complete( $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return new WP_REST_Response( $response, 200 );
    }

    public function handle_embed_request( WP_REST_Request $request ) {
        $security = SNN_AI_API_System::get_instance()->get_security();
        $rate_limit_check = $security->check_rate_limit();
        if ( is_wp_error( $rate_limit_check ) ) {
            return $rate_limit_check;
        }
        $provider_manager = SNN_AI_API_System::get_instance()->get_provider_manager();
        $provider_name = $request->get_param( 'provider' );
        $provider = $provider_manager->get_provider( $provider_name );

        if ( ! $provider ) {
            return new WP_Error( 'invalid_provider', __( 'Invalid provider specified.', 'snn-ai-api-system' ), [ 'status' => 400 ] );
        }

        $args = $request->get_json_params();
        $response = $provider->embed( $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return new WP_REST_Response( $response, 200 );
    }

    public function handle_generate_image_request( WP_REST_Request $request ) {
        $security = SNN_AI_API_System::get_instance()->get_security();
        $rate_limit_check = $security->check_rate_limit();
        if ( is_wp_error( $rate_limit_check ) ) {
            return $rate_limit_check;
        }
        $provider_manager = SNN_AI_API_System::get_instance()->get_provider_manager();
        $provider_name = $request->get_param( 'provider' );
        $provider = $provider_manager->get_provider( $provider_name );

        if ( ! $provider ) {
            return new WP_Error( 'invalid_provider', __( 'Invalid provider specified.', 'snn-ai-api-system' ), [ 'status' => 400 ] );
        }

        $args = $request->get_json_params();
        $response = $provider->generate_image( $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return new WP_REST_Response( $response, 200 );
    }

    public function check_permissions() {
        return current_user_can( 'manage_options' );
    }
}