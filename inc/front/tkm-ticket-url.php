<?php 

defined('ABSPATH') || exit('NO Access');

class TKM_Ticket_Url{

    public static function all(){

     return  wc_get_endpoint_url('tickets' , '' , get_permalink(get_option('woocommerce_myaccount_page_id')));

    }

    public static function new(){

        return add_query_arg( ['action' => 'new'] , self::all());
    }

    public static function single($ticket_id){

        return  add_query_arg( ['action' => 'single' , 'ticket-id' => $ticket_id] , self::all() );



    }

}
