<?php
/**
 * Taxonomy data source for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Taxonomy_Source {
    
    public function get_data($query_args) {
        $terms = get_terms($query_args);
        $formatted_terms = [];
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $formatted_terms[] = $this->format_term_data($term);
            }
        }
        
        return $formatted_terms;
    }
    
    private function format_term_data($term) {
        return [
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'description' => $term->description,
            'count' => $term->count,
            'taxonomy' => $term->taxonomy,
            'url' => get_term_link($term),
            'meta' => $this->get_term_meta($term)
        ];
    }
    
    private function get_term_meta($term) {
        $meta = get_term_meta($term->term_id);
        $formatted_meta = [];
        
        foreach ($meta as $key => $value) {
            $formatted_meta[$key] = is_array($value) && count($value) === 1 ? $value[0] : $value;
        }
        
        return $formatted_meta;
    }
}