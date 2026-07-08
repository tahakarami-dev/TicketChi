<?php
defined('ABSPATH') || exit('NO Access');

class TKM_Front_AJAX
{

    public function __construct()
    {
        add_action('wp_ajax_tkm-submit-ticket', [$this, 'submit_ticket']);
        add_action('wp_ajax_nopriv_tkm-submit-ticket', [$this, 'submit_ticket']);
        add_action('wp_ajax_tkm-submit-reply', [$this, 'reply_ticket']);
        add_action('wp_ajax_nopriv_tkm-submit-reply', [$this, 'reply_ticket']);
        add_action('wp_ajax_tkm_submit_rating', [$this, 'submit_rating']);
        add_action('wp_ajax_nopriv_tkm_submit_rating', [$this, 'submit_rating']);
    }

    public function submit_ticket()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tkm_ajax_nonce')) {
            $this->make_response(['success' => false, 'result' => 'درخواست نامعتبر است.']);
        }

        $file = isset($_FILES['file']) ? $_FILES['file'] : null;
        $upload_result = null;

        if ($file) {
            $uploader = new TKM_Upload_File($file);
            $upload_result = $uploader->upload();

            if (!$upload_result['success']) {
                $this->make_response(['success' => false, 'result' => $upload_result['message']]);
            }
        }

        $user_id = get_current_user_id();
        $ticket_data = [];

        $ticket_data['title'] = !empty($_POST['title_ticket']) ? sanitize_text_field($_POST['title_ticket']) : 'بدون عنوان';
        $ticket_data['body'] = wp_kses_post($_POST['content']);
        $ticket_data['creator_id'] = $user_id;
        $ticket_data['status'] = 'open';
        $ticket_data['priority'] = sanitize_text_field($_POST['priority']);
        $ticket_data['product'] = sanitize_text_field($_POST['user_purchased_products']);
        $ticket_data['edd_product'] = sanitize_text_field($_POST['edd_purchased_products']);
        $ticket_data['department_id'] = sanitize_text_field($_POST['child-department']);

        if ($upload_result && !empty($upload_result['urls'])) {
            $ticket_data['file'] = tkm_files_encode(array_map('esc_url_raw', $upload_result['urls']));
        }
        $voice_data = isset($_POST['audioData']) ? $_POST['audioData'] : null;
        $voice_upload_result = null;


        if ($voice_data) {
            $voice_uploader = new TKM_Upload_Voice($voice_data);
            $voice_upload_result = $voice_uploader->upload();

            if (!$voice_upload_result['success']) {
                $this->make_response(['success' => false, 'result' => $voice_upload_result['message']]);
            }
        }
        if ($voice_upload_result && isset($voice_upload_result['url'])) {
            $ticket_data['voice'] = esc_url($voice_upload_result['url']);
        }

        $ticket_manager = new TKM_Ticket_Manager();
        $ticket = $ticket_manager->insert($ticket_data);

        if (isset($ticket['ticket_id'])) {
            do_action('tkm_submit_ticket', $ticket['ticket_id']);
            $this->make_response(['success' => true, 'result' => TKM_Ticket_Url::all()]);
        } else {
            error_log('Ticket creation failed: ' . print_r($ticket, true));
            $this->make_response(['success' => false, 'result' => $ticket]);
        }
    }

    public function make_response($result)
    {
        if (is_array($result)) {
            wp_send_json($result);
        } else {
            wp_die($result);
        }
    }
    public function reply_ticket()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tkm_ajax_nonce')) {
            $this->make_response(['success' => false, 'result' => 'درخواست نامعتبر است.']);
        }

        $user_id = get_current_user_id();
        $ticket_id = $_POST['ticket_id'];

        $ticket_manager = new TKM_Ticket_Manager();
        $ticket = $ticket_manager->get_info_ticket($ticket_id);

        if (!$ticket || $ticket->status == 'finish') {
            $this->make_response(['success' => false, 'result' => 'خطایی رخ داده است']);
        }

        $data_reply = [
            'body' => !empty($_POST['body']) ? $_POST['body'] : '',
            'creator_id' => $user_id,
            'file' => null,
            'voice' => null,
        ];

        $status = !empty($_POST['status']) ? $_POST['status'] : 'open';

        if (isset($_FILES['file'])) {
            $uploader = new TKM_Upload_File($_FILES['file']);
            $upload_result = $uploader->upload();
            if (!$upload_result['success']) {
                $this->make_response(['success' => false, 'result' => $upload_result['message']]);
            }
            if (!empty($upload_result['urls'])) {
                $data_reply['file'] = tkm_files_encode(array_map('esc_url_raw', $upload_result['urls']));
            }
        }

        $ticket_manager->update_status($ticket_id, $status);

        if (!empty($_POST['audioData'])) {
            $voice_uploader = new TKM_Upload_Voice($_POST['audioData']);
            $voice_upload_result = $voice_uploader->upload();
            if ($voice_upload_result['success']) {
                $data_reply['voice'] = esc_url($voice_upload_result['url']);
            }
        }

        $reply_manager = new TKM_Reply_Manager($ticket_id);
        $insert = $reply_manager->insert_reply($data_reply);

        if (is_numeric($insert)) {
            $ticket_manager->update_reply_date($ticket_id);
            $replies = $reply_manager->get_replys();
            ob_start();
            include TKM_VIEWS_PATH . 'front/reply.php';
            $replies_html = ob_get_clean();

            $this->make_response(['success' => true, 'result' => 'پاسخ ثبت شد', 'replies_html' => $replies_html, 'status_update' => get_status_html($status)]);
        } else {
            $this->make_response(['success' => false, 'result' => $insert]);
        }
    }
    public function submit_rating()
    {
        if (!isset($_POST['rating'], $_POST['ticket_id'])) {
            wp_send_json_error(['message' => 'ورودی‌ها نامعتبر است.']);
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'tkm_ratings'; 

        $user_id = get_current_user_id();
        $ticket_id = intval($_POST['ticket_id']);
        $rating = intval($_POST['rating']);

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM $table_name WHERE ticket_id = %d AND user_id = %d",
            $ticket_id,
            $user_id
        ));

        if ($existing) {
            wp_send_json_error(['message' => 'شما قبلا برای این تیکت امتیاز داده‌اید.']);
            return;
        }

        $result = $wpdb->insert(
            $table_name,
            [
                'ticket_id' => $ticket_id,
                'user_id'   => $user_id,
                'rating'    => $rating,
            ],
            ['%d', '%d', '%d']
        );

        if ($result) {
            wp_send_json_success(['message' => 'امتیاز با موفقیت ثبت شد.']);
        } else {
            wp_send_json_error(['message' => 'خطا در ثبت امتیاز.']);
        }
    }
}
