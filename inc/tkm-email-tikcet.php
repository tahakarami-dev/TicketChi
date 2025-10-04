<?php 

// defined('ABSPATH') || exit('NO Access');

// class TKM_Send_email{

//     public function __construct()
//     {
//         add_filter( 'wp_mail_from_name',[$this, 'from_name'] );
//         add_filter( 'wp_mail_from' , [$this,'from_mail']);
//         add_filter( 'wp_mail_content_type' , [$this, 'content_type']);

//     }

//     public function content_type(){
//         return 'text/html';
//     }

//     public function from_mail(){
//       $email =  tkm_settings('email-from');
//      return $email ? $email: get_bloginfo('admin_email');
//     }

//     public function from_name(){
//       $name = tkm_settings('email-sender');
//       return $name ? $name : get_bloginfo('name');
//     }

//     public function send($email,$subject,$message){

//         wp_mail( $email, $subject, $message );

//     }

// }