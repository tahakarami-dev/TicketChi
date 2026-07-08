<?php

defined('ABSPATH') || exit('NO Access');

class TKM_MENU extends BASE_MENU
{
    public $tickets_list = NULL;
    public $from_admin_sms;
    private $wpdb;
    private $table;
    private $ticket_id = NULL;
    private $reply_table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkm_tickets';
        $this->ticket_id = isset($_GET['id']) ? $_GET['id'] : null;
        $this->reply_table = $wpdb->prefix . 'tkm_replies';
        $this->page_title = 'تیکت چی';
        $this->menu_title = 'تیکت چی';
        $this->menu_slug = 'tkm-settings ';
        $this->icon = TKM_ADMIN_ASSETS . 'image/messages.png';
        $this->has_sub_menu = true;
        $this->sub_item = [

            'settings' => [
                'page_title' => 'تنظیمات',
                'menu_title' => 'تنظیمات',
                'menu_slug' => 'tkm-settings',
                'callback' => '',
                'load' => [
                    'status' => false,

                ]

            ],
            'analysis' => [
                'page_title' => 'گزارش‌ ها',
                'menu_title' => 'گزارش‌ ها',
                'menu_slug' => 'tkm-analysis',
                'callback' => 'tkm_analysis',
                'load' => [
                    'status' => false,

                ]
            ],

            'ticket' => [
                'page_title' => 'لیست تیکت ها',
                'menu_title' => 'لیست تیکت ها',
                'menu_slug' => 'tkm-tickets',
                'callback' => 'tickets_page',
                'load' => [
                    'status' => true,
                    'callback_option' => 'tickets_screen_option',
                ]

            ],
            'departments' => [
                'page_title' => 'لیست دپارتمان ها',
                'menu_title' => 'لیست دپارتمان ها ',
                'menu_slug' => 'tkm-departments',
                'callback' => 'departments_page',
                'load' => [
                    'status' => false,
                ]
            ],
            'my_departments' => [
                'page_title' => ' تیکت های دپارتمان من  ',
                'menu_title' => ' تیکت های دپارتمان من  ',
                'menu_slug' => 'tkm-my-departments',
                'callback' => 'departments_page',
                'load' => [
                    'status' => false,
                ]
            ],
            'new-ticket' => [
                'page_title' => '  ارسال تیکت',
                'menu_title' => '  ارسال تیکت',
                'menu_slug' => 'tkm-new-ticket',
                'callback' => 'new_ticket_page',
                'load' => [
                    'status' => false,
                ],
            ],
            'edit-ticket' => [
                'page_title' => '  ویرایش تیکت',
                'menu_title' => '  ویرایش تیکت',
                'menu_slug' => 'tkm-edit-ticket',
                'callback' => 'edit_ticket_page',
                'load' => [
                    'status' => false,
                ],
            ],
        ];
        parent::__construct();
    }

    public function page()
    {
        echo '<h2>تیکت چی </h2>';
    }

    public function tickets_page()
    {
        include TKM_VIEWS_PATH . 'admin/ticket/main.php';
    }
    public function tkm_analysis()
    {
        include TKM_VIEWS_PATH . 'admin/analysis/analysis.php';
    }
    public function tickets_screen_option()
    {
        // add screen option 
        $args = [
            'label' => 'تعداد تیکت در هر صفحه :',
            'default' => 10,
            'option' => 'ticket_per_page',


        ];
        add_screen_option('per_page', $args);

        $this->tickets_list =  new TKM_Ticket_List();
    }

    public function departments_page()
    {

        $manager = new TKM_Admin_Department_Manager();
        $manager->page();
    }


    public function new_ticket_page()
    {
        $is_edit = false;

        if (isset($_POST['publish'])) {
            if (!isset($_POST['ticket_nonce']) || !wp_verify_nonce($_POST['ticket_nonce'], 'ticket_send')) {
                exit;
            }
            $current_user = get_current_user_id();
            $data = $_POST;
            $ticket_ids = [];

            $this->create_ticket($current_user, $data);

            if (!empty($ticket_ids)) {
                foreach ($ticket_ids as $id) {
                    echo '<div class="notice notice-success " style="padding: 10px; width:۹۵%">  تیکت با موفقیت ارسال شد.  </div>';
                }
            }
        }
        include TKM_VIEWS_PATH . 'admin/ticket/new.php';
    }
    public function create_ticket($creator_id, $data)
    {

        if (!empty($data['user-id']) && is_array($data['user-id']) &&  !empty($data['ticket-title']) &&  !empty($data['tkm-content']) &&  !empty($data['department_id'])) {
            $file_value = $this->collect_files(null, 'tkm_ticket_files');
            foreach ($data['user-id'] as $user_id) {
                $inserted = $this->wpdb->insert(
                    $this->table,
                    array(
                        'title' => sanitize_text_field($data['ticket-title']),
                        'body' => stripslashes_deep($data['tkm-content']),
                        'status' => $data['status'],
                        'priority' => $data['priority'],
                        'creator_id' => $creator_id,
                        'user_id' => $user_id,
                        'from_admin' => 1,
                        'department_id' => $data['department_id'],
                        'file' => $file_value,
                        'note' => isset($data['note']) ? sanitize_text_field($data['note']) : null

                    ),
                    array('%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s')
                );
                if ($inserted) {
                    $ticket_ids[] = $this->wpdb->insert_id;
                    if (isset($data['send-copy'])) {
                        do_action('tkm_submit_ticket', $ticket_ids);
                    }
                    echo '<div class="notice notice-success " style="padding: 10px; width:۹۵%">  تیکت با موفقیت ارسال شد.  </div>';
                    header("Location: ?page=tkm-tickets");
                } else {
                }
            }
        } else {
            echo '<div class="notice notice-error " style="padding: 10px; width:۹۵%"> ارسال تیکت با خطا مواجه شد لطفا موارد ضروری (*) را تکمیل نمایید </div>';
        }
    }

    public function get_from_adminsms()
    {
        return $from_admin_sms = true;
    }

    public function edit_ticket_page()
    {

        $reply_manager = new TKM_Reply_Manager($this->ticket_id);

        if (isset($_POST['publish'])) {

            if (!isset($_POST['ticket_nonce']) || !wp_verify_nonce($_POST['ticket_nonce'], 'ticket_send')) {
                exit('خطا نانس');
            }

            $data = $_POST;

            $replies = $reply_manager->get_replys();
            if (count($replies)) {
                foreach ($replies as $reply) {
                    if (isset($data['tkm-reply-body-' . $reply->ID])) {
                        if (!empty($data['tkm-reply-body-' . $reply->ID])) {

                            $reply_data = [
                                'ID' => $reply->ID,
                                'body' => stripslashes_deep($data['tkm-reply-body-' . $reply->ID]),
                                'file' => $this->collect_files($reply->file, 'tkm_reply_files_' . $reply->ID, 'remove_reply_file_' . $reply->ID)
                            ];

                            $this->update_reply($reply_data);
                        } else {
                            // delete reply
                            if ($reply_manager->delete_reply($reply->ID)) {
                                echo '<div class="notice notice-success " style="padding: 10px; width:۹۵%">  پاسخ با موفقیت حذف شد.  </div>';
                            }
                        }
                    }
                }
            }

            $data = $_POST;
            $ticket_ids = [];
            $this->update_ticket($data);
            $user_replyed = get_current_user_id();
            $insert_reply = 0;

            if (isset($data['reply_content']) && !empty($data['reply_content'])) {
                $reply_data = [
                    'ticket_id' => $this->ticket_id,
                    'creator_id' => $user_replyed,
                    'from_admin' => 1,
                    'body' => stripslashes_deep($data['reply_content']),
                    'file' => $this->collect_files(null, 'tkm_reply_files')
                ];
                $insert_reply =  $this->create_reply($reply_data);
                if ($insert_reply) {
                    $this->update_reply_date();
                    // پس از پاسخ ادمین، وضعیت تیکت به صورت خودکار به «پاسخ داده شده» تغییر می‌کند
                    // مگر اینکه ادمین به صورت دستی تیکت را بسته باشد
                    if (!isset($data['status']) || sanitize_text_field($data['status']) !== 'closed') {
                        $this->update_status('answerd');
                    }
                }
            }
            if (!empty($ticket_ids)) {
                foreach ($ticket_ids as $id) {
                    echo '<div class="notice notice-success " style="padding: 10px; width:۹۵%">  تیکت با موفقیت ارسال شد.  </div>';
                }
            }
        }


        $is_edit = true;
        $ticket =  $this->get_ticket();

        if (!empty($ticket) && !empty($ticket->ID) && is_numeric($ticket->ID)) {
            $reply_manager = new TKM_Reply_Manager($ticket->ID);
            $replies = $reply_manager->get_replys();
        } else {
            echo '<div class="notice notice-error" style="padding: 10px; width:۹۵٪">لطفا ابتدا <a href="admin.php?page=tkm-tickets">تیکت</a> مورد نظر را انتخاب کنید !!!</div>
';
            $reply_manager = null;
        }

        include TKM_VIEWS_PATH . 'admin/ticket/new.php';
    }

    public function create_reply($data)
    {

        $insert = $this->wpdb->insert(
            $this->reply_table,
            $data,
            ['%d', '%d', '%d', '%s', '%s']

        );

        if ($insert) {
            echo '<div class="notice notice-success " style="padding: 10px; width:۹۵%">  پاسخ با موفقیت ارسال  شد  </div>';
        }

        return $insert ? $this->wpdb->insert_id : null;
    }

    public function update_reply_date()
    {
        return   $this->wpdb->query($this->wpdb->prepare("UPDATE " . $this->table . " SET reply_date = NOW() WHERE ID = %d", $this->ticket_id));
    }

    public function update_status($status)
    {
        return $this->wpdb->update(
            $this->table,
            ['status' => $status],
            ['ID' => intval($this->ticket_id)],
            ['%s'],
            ['%d']
        );
    }

    /**
     * جمع‌آوری فایل‌های نهایی برای ذخیره:
     * فایل‌های موجود (منهای حذف‌شده‌ها) + فایل‌های تازه آپلودشده.
     * خروجی: مقدار JSON قابل ذخیره در ستون file یا null.
     */
    private function collect_files($current_value, $files_key, $remove_key = null)
    {
        $files = tkm_get_files($current_value);

        if ($remove_key && !empty($_POST[$remove_key]) && is_array($_POST[$remove_key])) {
            $remove = array_map('esc_url_raw', wp_unslash($_POST[$remove_key]));
            $files = array_values(array_filter($files, function ($url) use ($remove) {
                return !in_array($url, $remove, true);
            }));
        }

        if (!empty($_FILES[$files_key]) && !empty($_FILES[$files_key]['name'])) {
            $uploader = new TKM_Upload_File($_FILES[$files_key]);
            $result = $uploader->upload();

            if (!$result['success']) {
                echo '<div class="notice notice-error " style="padding: 10px; width:۹۵%">' . esc_html($result['message']) . '</div>';
            } else {
                foreach ($result['urls'] as $url) {
                    $files[] = esc_url_raw($url);
                }
            }
        }

        return tkm_files_encode($files);
    }

    public function update_ticket($data)
    {

        $creator_id = isset($data['creator-id']) ? intval($data['creator-id'][0]) : null;

        $user_id = isset($data['user-id']) && is_array($data['user-id']) ? intval($data['user-id'][0]) : null;

        if ($user_id &&   !empty($data['ticket-title']) &&  !empty($data['tkm-content']) &&  !empty($user_id) && !empty($creator_id) &&  !empty($data['department_id'])) {
            $current_ticket = $this->get_ticket();
            $file_value = $this->collect_files($current_ticket ? $current_ticket->file : null, 'tkm_ticket_files', 'remove_ticket_file');
            $updated = $this->wpdb->update(
                $this->table, 
                array(
                    'title' => sanitize_text_field($data['ticket-title']),
                    'body' => stripslashes_deep($data['tkm-content']),
                    'status' => sanitize_text_field($data['status']),
                    'priority' => sanitize_text_field($data['priority']),
                    'creator_id' => $creator_id,
                    'user_id' => $user_id,
                    'from_admin' => 1,
                    'department_id' => intval($data['department_id']),
                    'file' => $file_value,
                    'note' => isset($data['note']) ? sanitize_text_field($data['note']) : null,
                    'create_date' => sanitize_text_field($data['date_ticket'])
                ),
                array('ID' => intval($this->ticket_id)),
                array('%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s'), 
                array('%d') 
            );

            if ($updated !== false) {
                echo '<div class="notice notice-success " style="padding: 10px; width:۹۵%">  تیکت با موفقیت آپدیت شد  </div>';
            } else {
                echo '<div class="notice notice-error " style="padding: 10px; width:۹۵%">   مشکلی در آپدیت تیکت رخ داد  </div>';
            }
        } else {
            echo '<div class="notice notice-error " style="padding: 10px; width:۹۵%"> ارسال تیکت با خطا مواجه شد لطفا موارد ضروری (*) را تکمیل نمایید </div>';
        }
    }

    public function get_ticket()
    {
        if (!intval($this->ticket_id)) {
            return null;
        }
        return  $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE ID = %d", $this->ticket_id));
    }

    public function update_reply($data)
    {
        return  $reply_update = $this->wpdb->update(
            $this->reply_table,
            [
                'body' => $data['body'],
                'file' => $data['file']
            ],
            ['ID' => $data['ID']],
            ['%s', '%s'],
            ['%d']

        );
    }
}
