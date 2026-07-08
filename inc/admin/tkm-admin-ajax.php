<?php

defined('ABSPATH') || exit('NO Access');

class TKM_Admin_Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_tkm_search_user', [$this,  'search_users']);
    }

    public function search_users()
    {

        $term =  $_POST['term'];
        if (!$term) {
            wp_send_json_error();
        }
        $args = [
            'search' => '*' . esc_attr($term) . '*',
            'search_columns' => ['user_login', 'user_email', 'user_nicname']
        ];
        $user_query =  new WP_User_Query($args);
        $users = $user_query->get_results();

        $result = [];

        if (!empty($users)) {

            foreach ($users as $user) {

                $user_login = $user->user_login;
                $user_id = $user->ID;
                $result[] = [$user_id, $user_login];
            }
        }

        $this->make_response($result);
    }
    public function make_response($result)
    {

        if (is_array($result)) {

            wp_send_json($result);
        } else {

            echo $result;
        }

        wp_die();
    }
}
