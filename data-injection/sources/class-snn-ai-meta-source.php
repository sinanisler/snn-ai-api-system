<?php
/**
 * Meta data source for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_Meta_Source {
    
    public function get_data($query_args) {
        $meta_type = $query_args['meta_type'] ?? 'post';
        $meta_key = $query_args['meta_key'] ?? '';
        $meta_value = $query_args['meta_value'] ?? '';
        
        global $wpdb;
        
        $table = $wpdb->prefix . $meta_type . 'meta';
        
        $sql = "SELECT * FROM {$table} WHERE 1=1";
        $params = [];
        
        if ($meta_key) {
            $sql .= " AND meta_key = %s";
            $params[] = $meta_key;
        }
        
        if ($meta_value) {
            $sql .= " AND meta_value = %s";
            $params[] = $meta_value;
        }
        
        $sql .= " ORDER BY meta_id DESC";
        
        if (!empty($params)) {
            $sql = $wpdb->prepare($sql, $params);
        }
        
        $results = $wpdb->get_results($sql);
        
        return $this->format_meta_data($results);
    }
    
    private function format_meta_data($results) {
        $formatted = [];
        
        foreach ($results as $result) {
            $formatted[] = [
                'meta_id' => $result->meta_id,
                'object_id' => $result->{$this->get_object_id_field($result)},
                'meta_key' => $result->meta_key,
                'meta_value' => maybe_unserialize($result->meta_value)
            ];
        }
        
        return $formatted;
    }
    
    private function get_object_id_field($result) {
        if (property_exists($result, 'post_id')) {
            return 'post_id';
        } elseif (property_exists($result, 'user_id')) {
            return 'user_id';
        } elseif (property_exists($result, 'term_id')) {
            return 'term_id';
        } elseif (property_exists($result, 'comment_id')) {
            return 'comment_id';
        }
        
        return 'object_id';
    }
}