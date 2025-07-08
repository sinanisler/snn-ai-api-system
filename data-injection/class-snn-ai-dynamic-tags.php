<?php
/**
 * Dynamic tags for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Dynamic_Tags {
    
    private $tags = [];
    
    public function __construct() {
        $this->register_default_tags();
    }
    
    private function register_default_tags() {
        // Post tags
        $this->register_tag('post_title', [$this, 'get_post_title']);
        $this->register_tag('post_content', [$this, 'get_post_content']);
        $this->register_tag('post_excerpt', [$this, 'get_post_excerpt']);
        $this->register_tag('post_date', [$this, 'get_post_date']);
        $this->register_tag('post_author', [$this, 'get_post_author']);
        $this->register_tag('post_url', [$this, 'get_post_url']);
        $this->register_tag('post_type', [$this, 'get_post_type']);
        $this->register_tag('post_status', [$this, 'get_post_status']);
        $this->register_tag('post_categories', [$this, 'get_post_categories']);
        $this->register_tag('post_tags', [$this, 'get_post_tags']);
        
        // Term tags
        $this->register_tag('term_name', [$this, 'get_term_name']);
        $this->register_tag('term_description', [$this, 'get_term_description']);
        $this->register_tag('term_slug', [$this, 'get_term_slug']);
        $this->register_tag('term_url', [$this, 'get_term_url']);
        $this->register_tag('term_count', [$this, 'get_term_count']);
        
        // User tags
        $this->register_tag('user_name', [$this, 'get_user_name']);
        $this->register_tag('user_email', [$this, 'get_user_email']);
        $this->register_tag('user_display_name', [$this, 'get_user_display_name']);
        $this->register_tag('user_bio', [$this, 'get_user_bio']);
        $this->register_tag('user_url', [$this, 'get_user_url']);
        $this->register_tag('user_roles', [$this, 'get_user_roles']);
        
        // Meta tags
        $this->register_tag('meta', [$this, 'get_meta_value']);
        $this->register_tag('acf', [$this, 'get_acf_value']);
        
        // WordPress function tags
        $this->register_tag('get_the_title', 'get_the_title');
        $this->register_tag('get_the_content', 'get_the_content');
        $this->register_tag('get_the_excerpt', 'get_the_excerpt');
        $this->register_tag('get_the_date', 'get_the_date');
        $this->register_tag('get_the_author', 'get_the_author');
        $this->register_tag('get_permalink', 'get_permalink');
        
        // Allow other plugins to register tags
        do_action('snn_ai_register_dynamic_tags', $this);
    }
    
    public function register_tag($name, $callback) {
        $this->tags[$name] = $callback;
    }
    
    public function resolve($tag, $object) {
        // Handle meta tags with parameters
        if (strpos($tag, ':') !== false) {
            $parts = explode(':', $tag, 2);
            $tag_name = $parts[0];
            $parameter = $parts[1];
            
            if (isset($this->tags[$tag_name])) {
                return call_user_func($this->tags[$tag_name], $object, $parameter);
            }
        }
        
        // Handle direct function calls
        if (strpos($tag, 'get_post_meta:') === 0) {
            $meta_key = substr($tag, 14);
            return $this->get_post_meta_value($object, $meta_key);
        }
        
        // Handle regular tags
        if (isset($this->tags[$tag])) {
            return call_user_func($this->tags[$tag], $object);
        }
        
        // Try to call WordPress function directly
        if (function_exists($tag)) {
            return call_user_func($tag, $object);
        }
        
        return '';
    }
    
    public function get_available_tags() {
        return array_keys($this->tags);
    }
    
    // Post tag methods
    public function get_post_title($post) {
        return get_the_title($post);
    }
    
    public function get_post_content($post) {
        return get_the_content(null, false, $post);
    }
    
    public function get_post_excerpt($post) {
        return get_the_excerpt($post);
    }
    
    public function get_post_date($post, $format = null) {
        return get_the_date($format, $post);
    }
    
    public function get_post_author($post) {
        return get_the_author_meta('display_name', $post->post_author);
    }
    
    public function get_post_url($post) {
        return get_permalink($post);
    }
    
    public function get_post_type($post) {
        return get_post_type($post);
    }
    
    public function get_post_status($post) {
        return get_post_status($post);
    }
    
    public function get_post_categories($post) {
        $categories = get_the_category($post->ID);
        return wp_list_pluck($categories, 'name');
    }
    
    public function get_post_tags($post) {
        $tags = get_the_tags($post->ID);
        return $tags ? wp_list_pluck($tags, 'name') : [];
    }
    
    // Term tag methods
    public function get_term_name($term) {
        return $term->name;
    }
    
    public function get_term_description($term) {
        return $term->description;
    }
    
    public function get_term_slug($term) {
        return $term->slug;
    }
    
    public function get_term_url($term) {
        return get_term_link($term);
    }
    
    public function get_term_count($term) {
        return $term->count;
    }
    
    // User tag methods
    public function get_user_name($user) {
        return $user->user_login;
    }
    
    public function get_user_email($user) {
        return $user->user_email;
    }
    
    public function get_user_display_name($user) {
        return $user->display_name;
    }
    
    public function get_user_bio($user) {
        return get_user_meta($user->ID, 'description', true);
    }
    
    public function get_user_url($user) {
        return $user->user_url;
    }
    
    public function get_user_roles($user) {
        return $user->roles;
    }
    
    // Meta tag methods
    public function get_meta_value($object, $meta_key) {
        if (is_object($object) && isset($object->ID)) {
            return get_post_meta($object->ID, $meta_key, true);
        }
        
        if (is_object($object) && isset($object->term_id)) {
            return get_term_meta($object->term_id, $meta_key, true);
        }
        
        return '';
    }
    
    public function get_acf_value($object, $field_name) {
        if (!function_exists('get_field')) {
            return '';
        }
        
        if (is_object($object) && isset($object->ID)) {
            return get_field($field_name, $object->ID);
        }
        
        if (is_object($object) && isset($object->term_id)) {
            return get_field($field_name, "term_{$object->term_id}");
        }
        
        return '';
    }
    
    private function get_post_meta_value($post, $meta_key) {
        if (is_object($post) && isset($post->ID)) {
            return get_post_meta($post->ID, $meta_key, true);
        }
        
        return '';
    }
    
    public function format_value($value, $format = null) {
        if (!$format) {
            return $value;
        }
        
        switch ($format) {
            case 'date':
                return date_i18n(get_option('date_format'), strtotime($value));
                
            case 'time':
                return date_i18n(get_option('time_format'), strtotime($value));
                
            case 'datetime':
                return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($value));
                
            case 'number':
                return number_format_i18n($value);
                
            case 'currency':
                return '$' . number_format($value, 2);
                
            case 'uppercase':
                return strtoupper($value);
                
            case 'lowercase':
                return strtolower($value);
                
            case 'capitalize':
                return ucwords($value);
                
            case 'truncate':
                return wp_trim_words($value, 30);
                
            case 'strip_tags':
                return strip_tags($value);
                
            case 'escape_html':
                return esc_html($value);
                
            case 'wpautop':
                return wpautop($value);
                
            default:
                return apply_filters('snn_ai_format_dynamic_tag_value', $value, $format);
        }
    }
}