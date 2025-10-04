<?php 

defined('ABSPATH') || exit('NO Access');

class TKM_Admin_Ajax{
    public function __construct()
    {
        d3f39699b20b2c4dd150b133079e::bd6d8bdf1833fa06bdb297d6ef97e($this);
    }

    public function search_users(){

       $term =  $_POST['term'];
       if(!$term){
        wp_send_json_error();
       }
       $args = [
        'search' => '*' . esc_attr($term) . '*',
        'search_columns' => ['user_login' , 'user_email' ,'user_nicname']
       ];
      $user_query =  new WP_User_Query($args);
      $users = $user_query->get_results();

      $result = [];

      if(!empty($users)){

        foreach($users as $user){

            $user_login = $user->user_login;
            $user_id = $user->ID;
            $result[] = [$user_id , $user_login];

        }

      }

      $this->make_response($result);


    }
    public function make_response($result){

        if(is_array($result)){

            wp_send_json($result);

        }else{

            echo $result;

        }

        wp_die();


    }
}
