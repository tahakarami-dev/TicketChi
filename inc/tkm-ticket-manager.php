<?php

defined('ABSPATH') || exit('NO Access');

class TKM_Ticket_Manager
{
    private $wpdb;
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkm_tickets';
    }

    public function insert($data)
    {
        $errors = [];

        if (empty(intval($data['department_id']))) {
            $errors[] = 'لطفا ابتدا نوع تیکت را انتخاب نمایید';
        }

        if (empty($data['body'])) {
            $errors[] = 'لطفا محتوا تیکت را وارد نمایید';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $this->wpdb->insert( 
            $this->table,
            [
                'title' => sanitize_text_field($data['title']),
                'body' =>  wp_kses_post($data['body']),
                'creator_id' => $data['creator_id'] ? $data['creator_id'] : NULL,
                'user_id' => $data['user_id'] ? $data['user_id'] : NULL,
                'department_id' => $data['department_id'],
                'status' => $data['status'],
                'priority' => $data['priority'] ? $data['priority'] : 'medium',
                'create_date' => date("Y-m-d H:i:s"),
                'reply_date' => date("Y-m-d H:i:s"),
                'file' => $data['file'] ? $data['file'] : NULL,
                'voice' => $data['voice'] ? $data['voice'] : NULL, // اضافه کردن فیلد و
                'product' => $data['product'] ? $data['product'] : NULL,
        
            ],
            ['%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s','%d'] // اضافه کردن نوع برای ویس
        );

        $insert_id = $this->wpdb->insert_id;
        return ['ticket_id' => $insert_id];
    }

    public function get_tickets($user_id, $type = NULL, $status = NULL, $orderby = NULL, $page_num = NULL, $priority = NULL)
    {
        if (!intval($user_id)) {
            return [];
        }
    
        $args = [];
        $conditions = [];
    
        // Type filter
        switch ($type) {
            case 'send': // تیکت‌هایی که کاربر ایجاد کرده
                $conditions[] = "creator_id = %d";
                $args[] = $user_id;
                break;
    
            case 'get': // تیکت‌هایی که ادمین برای کاربر ارسال کرده
                $conditions[] = "user_id = %d AND from_admin = 1";
                $args[] = $user_id;
                break;
    
            default: // تمامی تیکت‌ها (ارسال شده و دریافت شده)
                $conditions[] = "(creator_id = %d OR user_id = %d)";
                $args[] = $user_id;
                $args[] = $user_id;
                break;
        }
    
        // Priority filter
        if ($priority !== NULL && $priority !== 'all-priority') {
            $conditions[] = "priority = %s";
            $args[] = $priority;
        }
    
        // Status filter
        if ($status !== NULL && $status !== 'all') {
            $conditions[] = "status = %s";
            $args[] = $status;
        }
    
        // Order by filter
        switch ($orderby) {
            case 'create-date':
                $orderby_sql = "ORDER BY create_date DESC";
                break;
    
            case 'reply-date':
            default:
                $orderby_sql = "ORDER BY reply_date DESC";
                break;
        }
    
        // Pagination filter
        $page_sql = "";
        if ($page_num) {
            $per_page = 10;
            $page_sql = "LIMIT %d";
            $args[] = $per_page;
    
            if ($page_num != 1) {
                $offset = ($page_num - 1) * $per_page;
                $page_sql .= " OFFSET %d";
                $args[] = $offset;
            }
        }
    
        // Combine conditions
        $where_sql = implode(' AND ', $conditions);
    
        // ساختن کوئری نهایی
        $query = "SELECT * FROM " . $this->table;
        if (!empty($where_sql)) {
            $query .= " WHERE " . $where_sql;
        }
        $query .= " " . $orderby_sql . " " . $page_sql;
    
        // اجرای کوئری و بازگشت نتایج
        return $this->wpdb->get_results($this->wpdb->prepare($query, $args));
    }
    
    public function ticket_count($user_id, $type = NULL, $status = NULL)
{
    if (!intval($user_id)) {
        return 0;
    }

    $args = [];
    $conditions = [];

    // فیلتر نوع
    switch ($type) {
        case 'send':
            $conditions[] = "creator_id = %d";
            $args[] = $user_id;
            break;
        case 'get':
            $conditions[] = "user_id = %d AND from_admin = 1";
            $args[] = $user_id;
            break;
        default:
            $conditions[] = "(user_id = %d OR creator_id = %d)";
            $args[] = $user_id;
            $args[] = $user_id;
            break;
    }

    // فیلتر وضعیت
    if ($status && $status !== 'all') {
        $conditions[] = "status = %s";
        $args[] = $status;
    }

    // ساخت کوئری
    $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE " . implode(" AND ", $conditions);

    // اجرای کوئری
    return $this->wpdb->get_var($this->wpdb->prepare($sql, $args));
}



    public function get_info_ticket($ticket_id)
    {
        if (!intval($ticket_id)) {
            return NULL;
        }

        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE ID = %d", $ticket_id));
    }

    public function update_status($ticket_id, $status)
    {
        return $this->wpdb->update($this->table, ['status' => $status], ['ID' => $ticket_id], ['%s'], ['%d']);
    }

    public function update_reply_date($ticket_id)
    {
        $date = date("Y-m-d H:i:s");
        return $this->wpdb->query($this->wpdb->prepare("UPDATE " . $this->table . " SET reply_date = %s WHERE ID = %d", $date, $ticket_id));
    }


    public function get_count_tickets(){
       return $this->wpdb->get_var("SELECT COUNT(*) FROM " .$this->table);
    }

    public function open_tickets(){
        return $this->wpdb->get_var("SELECT COUNT(*) FROM " .$this->table. " WHERE status='open' ");
    }

    public function cloesd_tickets(){
        return $this->wpdb->get_var("SELECT COUNT(*) FROM " .$this->table. " WHERE status='cloesd' ");
    }
    public function answerd_tickets(){
        return $this->wpdb->get_var("SELECT COUNT(*) FROM " .$this->table. " WHERE status='answerd' ");
    }
    public function has_user_rated_ticket($ticket_id) {
        if (!is_user_logged_in()) {
            return false; // کاربر وارد نشده است
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'tkm_ratings';
        $user_id = get_current_user_id();
    
        // بررسی ثبت امتیاز برای کاربر و تیکت خاص
        $rating_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM $table_name WHERE ticket_id = %d AND user_id = %d",
            $ticket_id, $user_id
        ));
    
        return !empty($rating_exists);
    }
    public function get_ticket_rating($ticket_id) {
        if (!is_user_logged_in()) {
            return 0; // مقدار پیش‌فرض برای کاربران وارد نشده
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'tkm_ratings'; // نام جدول امتیازات
        $user_id = get_current_user_id(); // شناسه کاربر فعلی
    
        // جستجو در جدول برای مقدار امتیاز
        $rating = $wpdb->get_var($wpdb->prepare(
            "SELECT rating FROM $table_name WHERE ticket_id = %d AND user_id = %d",
            $ticket_id,
            $user_id
        ));
    
        return $rating ? intval($rating) : 0; // اگر امتیاز پیدا شد، مقدار آن برگردانده می‌شود
    }

    public function get_user_purchased_products($user_id) {
        // گرفتن سفارشات کاربر
        $customer_orders = wc_get_orders(array(
            'customer_id' => $user_id,
            'status' => array('wc-completed', 'wc-processing'), // وضعیت‌های خرید
            'limit' => -1 // تمام سفارشات
        ));
    
        $products = array();
    
        // استخراج محصولات از سفارشات
        foreach ($customer_orders as $order) {
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $product_name = $item->get_name();
                $products[$product_id] = $product_name;
            }
        }
    
        return $products;
    }
    

  
}
