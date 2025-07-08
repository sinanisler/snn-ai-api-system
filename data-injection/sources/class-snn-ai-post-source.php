<?php
/**
 * Post data source for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Post_Source {
    
    public function get_data($query_args) {
        $query = new WP_Query($query_args);
        $posts = [];
        
        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = $this->format_post_data(get_post());
        }
        
        wp_reset_postdata();
        return $posts;
    }
    
    private function format_post_data($post) {
        return [
            'id' => $post->ID,
            'title' => get_the_title($post),
            'content' => get_the_content(null, false, $post),
            'excerpt' => get_the_excerpt($post),
            'date' => get_the_date('', $post),
            'author' => get_the_author_meta('display_name', $post->post_author),
            'url' => get_permalink($post),
            'type' => get_post_type($post),
            'status' => get_post_status($post),
            'categories' => $this->get_post_categories($post),
            'tags' => $this->get_post_tags($post),
            'meta' => $this->get_post_meta($post)
        ];
    }
    
    private function get_post_categories($post) {
        $categories = get_the_category($post->ID);
        return wp_list_pluck($categories, 'name');
    }
    
    private function get_post_tags($post) {
        $tags = get_the_tags($post->ID);
        return $tags ? wp_list_pluck($tags, 'name') : [];
    }
    
    private function get_post_meta($post) {
        $meta = get_post_meta($post->ID);
        $formatted_meta = [];
        
        foreach ($meta as $key => $value) {
            if (substr($key, 0, 1) !== '_') { // Skip private meta
                $formatted_meta[$key] = is_array($value) && count($value) === 1 ? $value[0] : $value;
            }
        }
        
        return $formatted_meta;
    }
}