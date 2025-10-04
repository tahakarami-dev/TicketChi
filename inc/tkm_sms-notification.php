<?php

defined('ABSPATH') || exit('NO Access');

class TKM_SMS_Notification
{
    public function __construct()
    {
        add_action('tkm_submit_ticket', [$this, 'submit_ticket']);
    }

    public function submit_ticket($ticket_id)
    {
        // بررسی فعال بودن ارسال SMS
        if (!tkm_settings('user_create_sms')) {
            return NULL;
        }

        $this->send($ticket_id);
    }

    public function send($ticket_id)
    {
        $ticket = (new TKM_Ticket_Manager())->get_info_ticket($ticket_id);

        if (!$ticket) {
            return NULL;
        }

        $phone = $this->get_phone($ticket);
        $message = $this->get_message($ticket);

        if ($phone) {
            $service = tkm_settings('sms-service');
            $class = 'TKM_' . ucfirst($service);

            if (!$service || !class_exists($class)) {
                return NULL;
            }

            $code = tkm_settings('user_create_sms_pattern_code');
            $send_sms = (new $class($phone, $message, $code))->send();
            if($send_sms){
                var_dump($send_sms);
            }
        }
    }

    private function get_phone($ticket)
    {
        // تشخیص اینکه آیا در محیط ادمین هستیم یا فرانت‌اند
        $is_admin = is_admin();
    
        // اگر در محیط ادمین هستیم، از creator_id استفاده کن
        if (is_admin() && !wp_doing_ajax()) {
            return get_user_meta($ticket->user_id, tkm_settings('phone-service-key-user'), true);
        }
        else{
            return get_user_meta($ticket->creator_id, tkm_settings('phone-service-key-user'), true);

        }
        
        // اگر در محیط فرانت‌اند هستیم، از user_id استفاده کن
    }
    

    private function get_message($ticket)
    {
        $pattern = tkm_settings('user_create_pattern');
        $pattern = explode(PHP_EOL, $pattern);
        $pattern_array = [];

        $department_manager = new TKM_Admin_Department_Manager();
        $department = $department_manager->get_a_department($ticket->ID);

        foreach ($pattern as $code) {
            $code = trim($code); // حذف فاصله‌های اضافی

            switch ($code) {
                case '{{ticket_id}}':
                    $pattern_array['ticket_id'] = $ticket->ID;
                    break;

                case '{{title}}':
                    $pattern_array['title'] = $ticket->title;
                    break;

                case '{{department}}':
                    $pattern_array['department'] = $department->name;
                    break;

                case '{{status}}':
                    $pattern_array['status'] = get_status_name($ticket->status);
                    break;

                case '{{priority}}':
                    $pattern_array['priority'] = get_priority_name($ticket->priority);
                    break;

                case '{{date}}':
                    $pattern_array['date'] = $ticket->create_date;
                    break;

                default:
                    // کدهای اضافی که تطابق ندارند نادیده گرفته شوند
                    break;
            }
        }

        return $pattern_array;
    }
}