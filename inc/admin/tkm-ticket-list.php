<?php

defined('ABSPATH') || exit('NO Access');

if (! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class TKM_Ticket_List extends WP_List_Table
{

    private $wpdb;
    private $table;
    private $statues;

    public function __construct()
    {

        parent::__construct([
            'singular' => 'ticket',
            'plural' => 'tickets',
        ]);

        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkm_tickets';

        $this->statues = tkm_get_status();
    }

    public function get_columns()
    {

        $columns = [
            'cb'  => '<input type="checkbox" />',
            'title' => 'عنوان',
            'department_id' => 'دپارتمان',
            'creator_id' => 'ایجاد کننده ',
            'status' => ' وضعیت ',
            'priority' => ' اهمیت ',
            'create_date' => 'تاریخ ایجاد  ',
            'reply_date' => ' تاریخ آخرین پاسخ ',

        ];

        return $columns;
    }

    public function get_tickets()
    {
        $params = $_GET;
        $args = [];
        $sql = " WHERE 1=1";
        if (isset($params['department_id']) && $params['department_id'] !== '') {
            $sql .= " AND (department_id = %d)";
            $args[] = $params['department_id'];
        }
        if (isset($params['priority']) && $params['priority'] !== '') {
            $sql .= " AND (priority = %s)";
            $args[] = $params['priority'];
        }
        if (isset($params['creator_id']) && $params['creator_id'] !== '') {
            $sql .= " AND (creator_id = %d)";
            $args[] = $params['creator_id'];
        }
        if (isset($params['search']) && $params['search'] !== '') {
            $sql .= " AND (title LIKE '%" .$params['search'] ."%')";
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $sql .= " AND (status = %s)";
            $args[] = $params['status'];
        }

        switch(isset($params['orderby'])){
            case "create_date":
                $sql .= " ORDER BY create_date " .$params['order'];
                break;
                case "reply_date":
                    $sql .= " ORDER BY reply_date " .$params['order'];
                    break;
                
                    default:
                    $sql .= " ORDER BY reply_date DESC";
        }
        

        return  $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM " . $this->table .$sql,$args ) ,ARRAY_A);
    }

    public function ticket_count()
    {
        return count($this->get_tickets());

    }

    public function prepare_items()
    {
        // trash
        $this->trash_action();
        //delete
        $this->delete_action();
        //bulk action 
        $this->bulk_action();

        $this->items = $this->get_tickets();

        /* pagination */
        $per_page = $this->get_items_per_page('ticket_per_page', 20);
        $current_page = $this->get_pagenum();
        $total_items = $this->ticket_count();

        $this->items = array_slice($this->items, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args([
            'total_items' => $total_items, // total number of items
            'per_page'    => $per_page, // items to show on a page
        ]);
    }

    public function bulk_action() {
        $action = $this->current_action(); // دریافت عملیات انتخاب شده
        $action = str_replace('bulk-', '', $action); // حذف "bulk-" از عم
        

    
        // دریافت شناسه‌های انتخاب شده
        $ids = isset($_POST['id']) ? $_POST['id'] : [];
    
        if (count($ids)) {
            foreach ($ids as $id) {
                if ($action == 'delete') {
                     $this->delete_ticket($id);
                    (new TKM_Reply_Manager($id))->delete_replies(); // حذف پاسخ‌ها
                } else {
                    // به‌روزرسانی وضعیت تیکت
                  $game=  $this->update_ticket_status($id, $action);
                    if($game){
                    }
                   
                }
            }
    
            // پیام موفقیت‌آمیز
            TKM_Flash_Message::add_message('عملیات با موفقیت انجام شد');
        }
    }

    public function trash_action(){

        if(isset($_GET['id']) && $_GET['action'] == 'trash' && isset($_GET['action']) && isset($_GET['_wpnonce'])){

            if(!wp_verify_nonce( $_GET['_wpnonce'], 'tkm_trash_ticket' )){
                wp_die('نانس شما تایید نشد ');

            }
            

            $this->update_ticket_status($_GET['id'],'trash');
            

        }

    }

    public function delete_action(){

        if(isset($_GET['id']) && $_GET['action'] == 'delete' && isset($_GET['action']) && isset($_GET['_wpnonce'])){

            if(!wp_verify_nonce( $_GET['_wpnonce'], 'tkm_delete_ticket' )){
                wp_die('نانس شما تایید نشد ');
                

            }
            

            $this->delete_ticket($_GET['id']);
            
            

        }


    }

    public function delete_ticket($id){

        $this->wpdb->delete($this->table, ['id' => $id] , ['%d']);

    }

    public function update_ticket_status($id, $status){

        $this->wpdb->update($this->table, ['status' => $status] , ['id' => $id] , ['%s'], ['%d']);

    }

    public function column_creator_id($item)
    {

        $user_data = get_userdata($item['creator_id']);
        $creator = '<a href="admin.php?page=tkm-tickets&creator_id='.$item['creator_id'].'">' . $user_data->display_name . ' </a>';
        $actions = ['edit' => '<a href="' . get_edit_user_link($item['creator_id']) . ' "target="_blank">' . 'پروفایل' . '</a>'];
        return $creator . $this->row_actions($actions);
    }

    public function column_title($item)
    {

        $title = '<strong>' . $item['title'] . '</strong>';
        $actions = [
            'id' => sprintf('<span>' . 'آیدی' . ': %d </span>', absint( $item['ID'] )),
            'edit' => sprintf('<a href="?page=tkm-edit-ticket&id=%s"> ' . 'ویرایش' . ' </a>' , absint( $item['ID'] ))
        ];
        if(isset($_GET['status']) && $_GET['status'] =='trash' ){

            $nonce = wp_create_nonce( 'tkm_delete_ticket' );
            $actions['trash'] = sprintf(
                "<a href='?page=tkm-tickets&action=delete&id=%s&_wpnonce=%s'> " . '   پاک کردن برای همیشه' . " </a>",
                absint( $item['ID'] ),
                $nonce
            );
        

        }else{
            $nonce = wp_create_nonce( 'tkm_trash_ticket' );
            $actions['trash'] = sprintf(
                "<a href='?page=tkm-tickets&action=trash&id=%s&_wpnonce=%s'> " . '  زباله دان' . " </a>",
                absint( $item['ID'] ),
                $nonce
            );

        }
     
        return $title . $this->row_actions($actions);
    }

    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="id[]" value="%s"', $item['ID']);
    }

    public function column_default($item, $column_name)
    {

        switch ($column_name) {

            case 'ID':
                return $item[$column_name];
                break;
            case 'title':
                return $item[$column_name];
                break;

            case 'department_id':
                return '<a href="admin.php?page=tkm-tickets&department_id='.$item[$column_name].'">' . get_department_html($item[$column_name]) . '</a>';
                break;

            case 'creator_id':
                return $item[$column_name];
                break;

            case 'status':
                return '<span class="status-list" style="background-color:'.get_status_color($item[$column_name]).'">'. get_status_name($item[$column_name]) .'</span>';
                break;

            case 'priority':
                return '<div class="box_priority"><a class="tkm-priority-' . $item[$column_name] . '" href="admin.php?page=tkm-tickets&priority='.$item[$column_name].'">' . get_priority_name($item[$column_name]) . '</a></div>';
                break;

            case 'create_date':
                return  jdate($item[$column_name]);
                
                break;


            case 'reply_date':
                return jdate($item[$column_name]);
                break;
        }
    }

    public function get_sortable_columns(){
        return [
            'create_date' => ['create_date', true],
            'reply_date' => ['reply_date', true]

        ];
    }

     // To show bulk action dropdown
     function get_bulk_actions()
     {
             $actions = [];

             foreach($this->statues as $status){

                $actions ['bulk-' . $status['slug']] = $status['name'];


             }
             if(isset($_GET['status']) && $_GET['status'] =='trash' ){
                unset($actions['bulk-trash']);
                $actions['bulk-delete'] = 'حذف';
             }
             return $actions;
     }
}
