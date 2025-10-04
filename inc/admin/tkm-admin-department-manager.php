<?php

defined('ABSPATH') || exit('NO Access');

class TKM_Admin_Department_Manager
{

    private $wpdb;
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkm_departments';
    }

    public function page()
    {

        $answerable_manager = new TKM_Answerable_Mnager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // افزودن یا ویرایش دپارتمان
            if (isset($_POST['add_department_nonce']) && wp_verify_nonce($_POST['add_department_nonce'], 'add_department')) {

                // بررسی اگر شناسه دپارتمان وجود دارد یعنی عملیات ویرایش است
                if (isset($_POST['department_id']) && !empty($_POST['department_id'])) {
                    $department_id = intval($_POST['department_id']);
                    // اجرای کوئری ویرایش
                    $update = $this->update($department_id, $_POST);

                    if ($update) {
                        // به‌روزرسانی کاربران پاسخگو
                        echo '<div class="alert_message_success">
                        <p class="message_alert">   بروزرسانی دپارتمان با موفقیت انجام شد</p>
                    </div>';
                        // $answerable_manager->delete($department_id);
                        // if (!empty($_POST['department-answerabel'])) {
                        //     foreach ($_POST['department-answerabel'] as $user) {
                        //         $answerable_manager->insert(['department_id' => $department_id, 'user_id' => $user]);
                        //     }
                        // }
                    } else {
                        echo '<div class="alert_message_error">
                        <p class="message_alert">   بروزرسانی دپارتمان با خطا مواجه شد</p>
                    </div>';
                    }
                    wp_redirect(admin_url('admin.php?page=tkm-departments'));
                    echo '<div class="alert_message_error">
                    <p class="message_alert">   بروزرسانی دپارتمان با خطا مواجه شد</p>
                </div>';
                } else {
                    // اجرای کوئری افزودن جدید
                    $insert = $this->insert_department($_POST);

                    if ($insert) {
                        if (!empty($_POST['department-answerabel'])) {
                            foreach ($_POST['department-answerabel'] as $user) {
                                $answerable_manager->insert(['department_id' => $insert, 'user_id' => $user]);
                            }
                        }
                        echo '<div class="notice notice-success " style="padding: 10px; width:90%"> دپارتمان با موفقیت اضافه شد </div>';
                    } else {
                        echo '<div class="notice notice-success " style="padding: 10px; width:90%">خطا در افزودن دپارتمان</div>';
                    }
                    wp_redirect(admin_url('admin.php?page=tkm-departments'));
                }
            } else {
                exit('متاسفیم، نانس شما تایید نشد.');
            }
        } else {

            if (isset($_GET['action']) && $_GET['action'] == 'delete') {

                if (isset($_GET['delete_department_nonce']) && wp_verify_nonce($_GET['delete_department_nonce'], 'delete_department')) {
                    $this->delete($_GET['id']);
                    $answerable_manager->delete($_GET['id']);
                    echo '<div class="notice notice-success " style="padding: 10px; width:90%">  دپارتمان مورد نظر با موفقیت حذف شد </div>';
                } else {
                    echo '<div class="notice notice-success " style="padding: 10px; width:90%"> متاسفانه نانس شما تایید نشد </div>';
                }
                wp_redirect(admin_url('admin.php?page=tkm-departments'));
            } elseif (isset($_GET['action']) && $_GET['action'] == 'edit') {
                $departments = $this->get_department();
                $showdepartment = $this->get_a_department($_GET['id']);
                $answerable = $answerable_manager->get_by_department($showdepartment->ID);
                include TKM_VIEWS_PATH . 'admin/department/edit.php';
            } else {
                $departments = $this->get_department();
                include TKM_VIEWS_PATH . 'admin/department/main.php';
            }
        }
    }

    public function get_department()
    {
        return $this->wpdb->get_results("SELECT * FROM " . $this->table . " ORDER BY position");
    }

    public function get_a_department($id)
    {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE ID = %d", $id));
    }

    public function delete($id)
    {
        $this->wpdb->delete($this->table, ['ID' => $id], ['%d']);
    }
    public function update($id, $data)
    {
        $data = [
            'name' => sanitize_text_field($data['name']),
            'parent' => isset($data['parent']) ? intval($data['parent']) : 0,
            'position' => isset($data['position']) ? intval($data['position']) : 1,
            'description' => isset($data['description']) ? sanitize_textarea_field($data['description']) : null,
        ];

        $where = ['ID' => (int) $id];
        $data_format = ['%s', '%d', '%d', '%s'];
        $where_format = ['%d'];

        return $update_department =  $this->wpdb->update($this->table, $data, $where, $data_format, $where_format);

        if ($update_department) {
            echo '<div class="notice notice-warning " style="padding: 10px; width:90%"> دپارتمان با موفقیت بروزرسانی شد </div>';
        }
    }

    private function insert_department($data)
    {
        $data = [
            'name' => sanitize_text_field($data['name']),
            'parent' => isset($data['parent']) ? intval($data['parent']) : 0,
            'position' => isset($data['position']) ? intval($data['position']) : 1,
            'description' => isset($data['description']) ? sanitize_textarea_field($data['description']) : null,
        ];

        $data_format = ['%s', '%d', '%d', '%s'];

        $insert = $this->wpdb->insert($this->table, $data, $data_format);
        return $insert ? $this->wpdb->insert_id : null;
        if ($insert) {
            echo '<div class="notice notice-warning " style="padding: 10px; width:90%"> دپارتمان با موفقیت بروزرسانی شد </div>';
        }
    }
    public function get_parent_department()
    {
        return  $this->wpdb->get_results("SELECT * FROM " . $this->table . " WHERE parent = 0 ORDER BY position ");
    }

    public function get_child_department($parent_id)
    {

        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE parent = %d ORDER BY position ", $parent_id));
    }
}
