<?php
/**
 * User data source for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class SNN_AI_User_Source {
    
    public function get_data($query_args) {
        $users = get_users($query_args);
        $formatted_users = [];
        
        foreach ($users as $user) {
            $formatted_users[] = $this->format_user_data($user);
        }
        
        return $formatted_users;
    }
    
    private function format_user_data($user) {
        return [
            'id' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'display_name' => $user->display_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'url' => $user->user_url,
            'registered' => $user->user_registered,
            'roles' => $user->roles,
            'meta' => $this->get_user_meta($user)
        ];
    }
    
    private function get_user_meta($user) {
        $meta = get_user_meta($user->ID);
        $formatted_meta = [];
        
        foreach ($meta as $key => $value) {
            if (substr($key, 0, 1) !== '_') { // Skip private meta
                $formatted_meta[$key] = is_array($value) && count($value) === 1 ? $value[0] : $value;
            }
        }
        
        return $formatted_meta;
    }
}