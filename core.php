<?php
/*
Plugin Name:  تیکت‌چی
Description: با تیکت‌چی، مدیریت تیکت‌های پشتیبانی در وردپرس را متحول کنید. این افزونه قدرتمند، با ظاهری جذاب و مدرن و امکاناتی که واقعاً به کارتان می‌آید، تجربه‌ای حرفه‌ای و بی‌دردسر را برای شما و کاربرانتان رقم می‌زند. پشتیبانی را از حالت تکراری خارج کنید!
Version: 1.4.0
Author: طاها کرمی
Author URI: https://www.rtl-theme.com/author/taha-karami
*/

defined('ABSPATH') || exit('NO Access');

class Core
{

    private static $_instance = null;

    const MINIUM_PHP_VERSION = '7.4';

    public static function instance()
    {

        if (is_null(self::$_instance)) {

            self::$_instance = new self();
        }

        return  self::$_instance;
    }

    public function __construct()
    {

        if (version_compare(PHP_VERSION, self::MINIUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_php_notice']);
            return;
        }

        $this->constant();
        $this->init();
    }
    
    public function constant()
    {

        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        define('TKM_BASE_FILE', __FILE__);
        define('TKM_PATH', trailingslashit(plugin_dir_path(TKM_BASE_FILE)));
        define('TKM_URL', trailingslashit(plugin_dir_url(TKM_BASE_FILE)));
        define('TKM_ADMIN_ASSETS', trailingslashit(TKM_URL . 'assets/admin'));
        define('TKM_FRONT_ASSETS', trailingslashit(TKM_URL . 'assets/front'));
        define('TKM_INC_PATH', trailingslashit(TKM_PATH . 'inc'));
        define('TKM_VIEWS_PATH', trailingslashit(TKM_PATH . 'views'));
        $tkm_plugin_data =  get_plugin_data(TKM_BASE_FILE, '<');
        define('TKM_VER',  $tkm_plugin_data['Version']);
    }

    public function init()
    {
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
            $links[] = '<a href="' . admin_url('admin.php?page=tkm-settings') . '">تنظیمات</a>';
            return $links;
        });
        add_action('tkm_auto_close', [$this, 'tkm_auto_close_events']);


        require_once TKM_PATH . 'vendor/autoload.php';
        require_once TKM_INC_PATH . 'admin/codestar/codestar-framework.php';
        require_once TKM_INC_PATH . 'admin/tkm-settings.php';
        require_once  TKM_INC_PATH . 'functions.php';
        require_once  TKM_INC_PATH . 'admin/abstract/base-menu.php';

        register_activation_hook(TKM_BASE_FILE, [$this, 'active']);
        register_deactivation_hook(TKM_BASE_FILE, [$this, 'deactive']);

        tkm_settings();
        if (is_admin()) {

            new TKM_MENU();
            new TKM_Admin_Ajax();
            new TKM_Analysis();
        } else {
            new  TKM_SMS_Notification();
            new TKM_Shortcode_Router();
            new TKM_Shortcode_Url();
        }
        new TKM_Front_AJAX();
        new TKM_ASSETS();
        new TKM_WC_Dashboard();
    }

    public function active()
    {
        TKM_DB::create_table();

        if (! wp_next_scheduled('tkm_auto_close')) {
            wp_schedule_event(time(), 'daily', 'tkm_auto_close');
        }
    }

    public function tkm_auto_close_events()
    {
        $active = tkm_settings('close_auto_ticket');

        if (! $active) {
            return;
        }

        global $wpdb;
        $ticket_table = $wpdb->prefix . 'tkm_tickets';

        $date = date("Y-m-d H:i:s", strtotime("-7 days"));

        $tickets = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT ID FROM {$ticket_table} WHERE status != 'closed' AND reply_date < %s",
                $date
            )
        );

        if (!empty($tickets) && class_exists('TKM_Ticket_Manager')) {
            $ticket_manager = new TKM_Ticket_Manager();
            foreach ($tickets as $ticket_id) {
                $ticket_manager->update_status($ticket_id, 'closed');
            }
        }
    }
    public function deactive() {}

    public function admin_php_notice()
    { ?>
        <div class="notice notice-error">
            <p>افزونه تیکت چی برای اجرا صحیح نیاز به نسخه 7.4 به بالا دارد لطفا نسخه php هاست خود را ارتقا دهید
            </p>
        </div>
<?php
    }
}

Core::instance();
