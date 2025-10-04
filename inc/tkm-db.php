<?php

defined('ABSPATH') || exit('NO Access');

class TKM_DB
{
    public static function create_table()
    {
        global $wpdb;
        $departments = $wpdb->prefix . 'tkm_departments';
        $users = $wpdb->prefix . 'tkm_users';
        $tickets = $wpdb->prefix . 'tkm_tickets';
        $replies = $wpdb->prefix . 'tkm_replies';
        $ratings = $wpdb->prefix . 'tkm_ratings';



        $charset = $wpdb->get_charset_collate();

        $departments_sql = "CREATE TABLE IF NOT EXISTS $departments ( 
            ID bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(128) NOT NULL,
            parent bigint(20) NOT NULL DEFAULT '0',
            position int(11) NOT NULL DEFAULT '1',
            description varchar(512) DEFAULT NULL,
            PRIMARY KEY (ID), 
            INDEX parent (parent)
        ) ENGINE=InnoDB $charset;";

        $users_sql = "CREATE TABLE IF NOT EXISTS $users (
            ID bigint(20) NOT NULL AUTO_INCREMENT,
            department_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            PRIMARY KEY (ID),
            KEY department_id (department_id),
            KEY user_id (user_id)
        ) ENGINE=InnoDB $charset;";

        $replies_sql = "CREATE TABLE IF NOT EXISTS $replies (
            ID bigint(20) NOT NULL AUTO_INCREMENT,
            ticket_id bigint(20) NOT NULL,
            creator_id bigint(20) DEFAULT NULL,
            from_admin tinyint(1) DEFAULT NULL,
            body text NOT NULL,
            create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            voice varchar(512) DEFAULT NULL,
            file text DEFAULT NULL,
            PRIMARY KEY (ID),
            KEY ticket_id (ticket_id)
        ) ENGINE=InnoDB $charset;";

        $tickets_sql = "CREATE TABLE IF NOT EXISTS $tickets (
    ID bigint(20) NOT NULL AUTO_INCREMENT,
    title varchar(256) NOT NULL,
    body text NOT NULL,
    creator_id bigint(20) DEFAULT NULL,
    user_id bigint(20) DEFAULT NULL,
    user_name varchar(64) DEFAULT NULL,
    user_email varchar(128) DEFAULT NULL,
    user_phone varchar(16) DEFAULT NULL,
    from_admin tinyint(1) DEFAULT NULL,
    department_id bigint(20) NOT NULL,
    status varchar(64) NOT NULL,
    priority varchar(32) NOT NULL,
    create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reply_date varchar(19) DEFAULT NULL,
    voice varchar(512) DEFAULT NULL,
    file text DEFAULT NULL,
    note varchar(512) DEFAULT NULL,
    product bigint(20) DEFAULT NULL,
    PRIMARY KEY (ID),
    KEY title (title),
    KEY creator_id (creator_id),
    KEY user_id (user_id),
    KEY from_admin (from_admin),
    KEY department_id (department_id),
    KEY status (status)
) ENGINE=InnoDB $charset;";


        $ratings_sql = "CREATE TABLE IF NOT EXISTS $ratings ( 
            ID bigint(20) NOT NULL AUTO_INCREMENT, 
            ticket_id bigint(20) NOT NULL, 
            user_id bigint(20) NOT NULL, 
            rating tinyint(1) NOT NULL, 
            create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            PRIMARY KEY (ID), 
            KEY ticket_id (ticket_id), 
            KEY user_id (user_id) 
        ) ENGINE=InnoDB $charset;";



        if (!function_exists('dbDelta')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        }

        dbDelta($departments_sql);
        dbDelta($users_sql);
        dbDelta($replies_sql);
        dbDelta($tickets_sql);
        dbDelta($ratings_sql);
    }
}
