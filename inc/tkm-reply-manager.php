<?php

defined('ABSPATH') || exit('NO Access');

class TKM_Reply_Manager
{

    private $wpdb;
    private $table;
    private $ticket_id;

    public function __construct($ticket_id)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkm_replies';
        $this->ticket_id = $ticket_id;
    }

    public function insert_reply($data)
    {
        $errors = [];

        if (empty($data['body'])) {
            $errors[] = 'لطفا پاسخ تیکت را وارد نمایید';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $this->wpdb->insert($this->table, [
            'ticket_id' => $this->ticket_id,
            'body' => wp_kses_post($data['body']),
            'creator_id' => $data['creator_id'] ? $data['creator_id'] : NULL,
            'file' => $data['file'] ? $data['file'] : null,
            'voice' => $data['voice'] ? $data['voice'] : NULL,
        ], [
            '%d',
            '%s',
            '%d',
            '%s',
            '%s'
        ]);

        return $this->wpdb->insert_id;
    }

    public function get_replys()
    {
        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE ticket_id = %d ORDER BY create_date ASC", $this->ticket_id));
    }

    public function delete_reply($reply_id){
       return $this->wpdb->delete($this->table , ['ID' => $reply_id, ] ,['%d']);
    }

    public function delete_replies(){
       return  $this->wpdb->delete($this->table, ['ticket_id' => $this->ticket_id] ,['%d']);
    }

}
