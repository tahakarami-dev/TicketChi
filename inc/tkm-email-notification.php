<?php 

// defined('ABSPATH') || exit('NO Access');


// class TKM_Email_Notification{

//     public function __construct()
//     {
//         add_action('tkm_submit_ticket', [$this, 'submit_ticket']);


//     }

//     public function submit_ticket($ticket_id){

//         if(!tkm_settings('user_create_email')){
//             return null;
//         }

//         //send email

//         $ticket = (new TKM_Ticket_Manager())->get_tickets($ticket_id);
//         $email = $this->get_email($ticket);
//         $subject = "تیکت " . $ticket->ID . 'با موفقیت ثبت شد ';

//         if(is_email($email)){
//             $message = $this->get_message($ticket);
//           $send_email =  new TKM_Send_email();
//           $send_email->send($email , $subject,$message);

//         }


//     }

//     public function get_email($ticket){
//       return  get_userdata($ticket->creator_id)->user_email;
//     }

//     public function get_message($ticket){
//         $message = tkm_settings('pattern_email');
//         $deparement_manager = new TKM_Admin_Department_Manager();
//         $deparement = $deparement_manager->get_a_department($ticket->ID);

//         $search = [
//             '{{ticket_id}}',
//             '{{title}}',
//             '{{department}}',
//             '{{status}}',
//             '{{priority}}',
//             '{{date}}',
    
//         ];
//         $replace = [
//             $ticket->ID,
//             $ticket->title,
//             $deparement->name,
//             get_status_name($ticket->status),
//             get_priority_name( $ticket->priority),
//             $ticket->create_date

//         ];

//      return   str_replace($search,$replace,$message);
//     }

// }