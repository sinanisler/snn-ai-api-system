<?php
/**
 * Query builder for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Query_Builder {
    
    public function build($params) {
        $query_type = $params['query_type'] ?? 'posts';
        
        switch ($query_type) {
            case 'posts':
                return $this->build_posts_query($params);
                
            case 'terms':
                return $this->build_terms_query($params);
                
            case 'users':
                return $this->build_users_query($params);
                
            case 'meta':
                return $this->build_meta_query($params);
                
            default:
                return [];
        }
    }
    
    private function build_posts_query($params) {
        $query = [
            'post_type' => $params['post_type'] ?? 'post',
            'post_status' => $params['post_status'] ?? 'publish',
            'posts_per_page' => intval($params['posts_per_page'] ?? 10),
            'orderby' => $params['orderby'] ?? 'date',
            'order' => $params['order'] ?? 'DESC'
        ];
        
        if (!empty($params['meta_query'])) {
            $query['meta_query'] = $this->build_meta_query_array($params['meta_query']);
        }
        
        if (!empty($params['tax_query'])) {
            $query['tax_query'] = $this->build_tax_query_array($params['tax_query']);
        }
        
        if (!empty($params['author'])) {
            $query['author'] = intval($params['author']);
        }
        
        if (!empty($params['search'])) {
            $query['s'] = sanitize_text_field($params['search']);
        }
        
        if (!empty($params['date_query'])) {
            $query['date_query'] = $params['date_query'];
        }
        
        return $query;
    }
    
    private function build_terms_query($params) {
        $query = [
            'taxonomy' => $params['taxonomy'] ?? 'category',
            'hide_empty' => $params['hide_empty'] ?? true,
            'orderby' => $params['orderby'] ?? 'name',
            'order' => $params['order'] ?? 'ASC'
        ];
        
        if (!empty($params['number'])) {
            $query['number'] = intval($params['number']);
        }
        
        if (!empty($params['search'])) {
            $query['search'] = sanitize_text_field($params['search']);
        }
        
        if (!empty($params['parent'])) {
            $query['parent'] = intval($params['parent']);
        }
        
        if (!empty($params['meta_query'])) {
            $query['meta_query'] = $this->build_meta_query_array($params['meta_query']);
        }
        
        return $query;
    }
    
    private function build_users_query($params) {
        $query = [
            'orderby' => $params['orderby'] ?? 'login',
            'order' => $params['order'] ?? 'ASC'
        ];
        
        if (!empty($params['number'])) {
            $query['number'] = intval($params['number']);
        }
        
        if (!empty($params['role'])) {
            $query['role'] = sanitize_text_field($params['role']);
        }
        
        if (!empty($params['search'])) {
            $query['search'] = sanitize_text_field($params['search']);
        }
        
        if (!empty($params['meta_query'])) {
            $query['meta_query'] = $this->build_meta_query_array($params['meta_query']);
        }
        
        return $query;
    }
    
    private function build_meta_query($params) {
        return [
            'meta_type' => $params['meta_type'] ?? 'post',
            'meta_key' => $params['meta_key'] ?? '',
            'meta_value' => $params['meta_value'] ?? ''
        ];
    }
    
    private function build_meta_query_array($meta_query) {
        $built_query = [];
        
        if (isset($meta_query['relation'])) {
            $built_query['relation'] = $meta_query['relation'];
        }
        
        foreach ($meta_query as $key => $query) {
            if (is_array($query) && isset($query['key'])) {
                $built_query[] = [
                    'key' => sanitize_text_field($query['key']),
                    'value' => $query['value'],
                    'compare' => $query['compare'] ?? '=',
                    'type' => $query['type'] ?? 'CHAR'
                ];
            }
        }
        
        return $built_query;
    }
    
    private function build_tax_query_array($tax_query) {
        $built_query = [];
        
        if (isset($tax_query['relation'])) {
            $built_query['relation'] = $tax_query['relation'];
        }
        
        foreach ($tax_query as $key => $query) {
            if (is_array($query) && isset($query['taxonomy'])) {
                $built_query[] = [
                    'taxonomy' => sanitize_text_field($query['taxonomy']),
                    'field' => $query['field'] ?? 'term_id',
                    'terms' => $query['terms'],
                    'operator' => $query['operator'] ?? 'IN'
                ];
            }
        }
        
        return $built_query;
    }
    
    public function get_query_preview($params, $limit = 3) {
        $query = $this->build($params);
        
        // Limit results for preview
        switch ($params['query_type'] ?? 'posts') {
            case 'posts':
                $query['posts_per_page'] = $limit;
                break;
                
            case 'terms':
            case 'users':
                $query['number'] = $limit;
                break;
        }
        
        return $query;
    }
}