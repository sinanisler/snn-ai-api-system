<?php
/**
 * SNN AI Together AI Provider
 *
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SNN_AI_Together_AI_Provider extends SNN_AI_Abstract_Provider {
    protected $name = 'together-ai';
    protected $display_name = 'Together AI';
    protected $api_key_option = 'snn_ai_together_ai_api_key';
    protected $api_url_option = 'snn_ai_together_ai_api_url';
    protected $default_api_url = 'https://api.together.xyz/v1';

    public function __construct() {
        parent::__construct();
        $this->init_models();
    }

    protected function init_models() {
        $this->models = [
            'togethercomputer/llama-2-7b-chat' => [
                'name' => 'Llama 2 7B Chat',
                'description' => 'Llama 2 7B Chat model from Together AI.',
                'endpoints' => ['chat', 'complete'],
            ],
            'togethercomputer/llama-2-70b-chat' => [
                'name' => 'Llama 2 70B Chat',
                'description' => 'Llama 2 70B Chat model from Together AI.',
                'endpoints' => ['chat', 'complete'],
            ],
            'togethercomputer/redpajama-incite-7b-chat' => [
                'name' => 'RedPajama-INCITE-7B-Chat',
                'description' => 'RedPajama-INCITE-7B-Chat model from Together AI.',
                'endpoints' => ['chat', 'complete'],
            ],
        ];
    }

    public function chat( $args ) {
        $defaults = [
            'model'             => 'togethercomputer/llama-2-7b-chat',
            'messages'          => [],
            'max_tokens'        => 1024,
            'temperature'       => 0.7,
            'top_p'             => 0.7,
            'top_k'             => 50,
            'repetition_penalty' => 1,
            'stop'              => null,
        ];

        $args = wp_parse_args( $args, $defaults );

        $body = [
            'model'             => $args['model'],
            'messages'          => $args['messages'],
            'max_tokens'        => $args['max_tokens'],
            'temperature'       => $args['temperature'],
            'top_p'             => $args['top_p'],
            'top_k'             => $args['top_k'],
            'repetition_penalty' => $args['repetition_penalty'],
        ];

        if ( ! empty( $args['stop'] ) ) {
            $body['stop'] = $args['stop'];
        }

        $response = wp_remote_post( trailingslashit( $this->get_api_url() ) . 'chat/completions', [
            'headers' => $this->get_headers(),
            'body'    => wp_json_encode( $body ),
            'timeout' => 60,
        ] );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $response_body = wp_remote_retrieve_body( $response );
        $result        = json_decode( $response_body, true );

        if ( isset( $result['error'] ) ) {
            return new WP_Error( 'api_error', $result['error']['message'] );
        }

        return [
            'content' => $result['choices'][0]['message']['content'],
        ];
    }

    public function complete( $args ) {
        $defaults = [
            'model'             => 'togethercomputer/llama-2-7b-chat',
            'prompt'            => '',
            'max_tokens'        => 1024,
            'temperature'       => 0.7,
            'top_p'             => 0.7,
            'top_k'             => 50,
            'repetition_penalty' => 1,
            'stop'              => null,
        ];

        $args = wp_parse_args( $args, $defaults );

        $body = [
            'model'             => $args['model'],
            'prompt'            => $args['prompt'],
            'max_tokens'        => $args['max_tokens'],
            'temperature'       => $args['temperature'],
            'top_p'             => $args['top_p'],
            'top_k'             => $args['top_k'],
            'repetition_penalty' => $args['repetition_penalty'],
        ];

        if ( ! empty( $args['stop'] ) ) {
            $body['stop'] = $args['stop'];
        }

        $response = wp_remote_post( trailingslashit( $this->get_api_url() ) . 'completions', [
            'headers' => $this->get_headers(),
            'body'    => wp_json_encode( $body ),
            'timeout' => 60,
        ] );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $response_body = wp_remote_retrieve_body( $response );
        $result        = json_decode( $response_body, true );

        if ( isset( $result['error'] ) ) {
            return new WP_Error( 'api_error', $result['error']['message'] );
        }

        return [
            'text' => $result['choices'][0]['text'],
        ];
    }

    public function embed( $text, $args = [] ) {
        return new WP_Error( 'not_supported', __( 'Embeddings are not supported by Together AI.', 'snn-ai-api-system' ) );
    }

    public function generate_image( $args ) {
        return new WP_Error( 'not_supported', __( 'Image generation is not supported by Together AI.', 'snn-ai-api-system' ) );
    }

    protected function get_headers() {
        return [
            'Authorization' => 'Bearer ' . $this->get_api_key(),
            'Content-Type'  => 'application/json',
        ];
    }
}
