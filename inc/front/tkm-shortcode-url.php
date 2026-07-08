<?php

defined('ABSPATH') || exit('NO Access');

class TKM_Shortcode_Url
{

    public static function base()
    {
        return get_permalink();
    }

    public static function new()
    {
        return add_query_arg(['action' => 'new'], self::base());
    }

    public static function single($ticket_id)
    {
        return add_query_arg([
            'action'    => 'single',
            'ticket-id' => $ticket_id
        ], self::base());
    }
}
