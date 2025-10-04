<?php
/*
Plugin Name:  تیکت‌چی
Description: تیکت‌چی یک افزونه قدرتمند مدیریت تیکت‌ها در وردپرس است که با طراحی جذاب و قابلیت‌های کاربردی، تجربه‌ای حرفه‌ای را برای شما و کاربران فراهم می‌کند.
Version: 1.1.2
Author: Taha karami
*/

defined('ABSPATH') || exit('NO Access');

require_once __DIR__.'/activatezhk/validate-locked.php';

class Core
{

    private static $_instance = null;

    const MINIUM_PHP_VERSION = '7.2';

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

        require_once TKM_PATH . 'vendor/autoload.php';
        require_once TKM_INC_PATH . 'admin/codestar/codestar-framework.php';
        require_once TKM_INC_PATH . 'admin/tkm-settings.php';
        require_once  TKM_INC_PATH . 'functions.php';





        register_activation_hook(TKM_BASE_FILE, [$this, 'active']);
        register_deactivation_hook(TKM_BASE_FILE, [$this, 'deactive']);

   
    

    
        tkm_settings();

        if(is_admin()){

            new TKM_MENU();

            new TKM_Admin_Ajax();

            new TKM_Analysis();

        }else{
            d3f39699b20b2c4dd150b133079e::adb85ced23ff1f05e3b26d022fa83f();

            

        }
        new TKM_Front_AJAX();
        new TKM_ASSETS();
        d3f39699b20b2c4dd150b133079e::a6f06590baff95c95904183ca9e();

     
    }

    public function active()
    {

        TKM_DB::create_table();

        if (! wp_next_scheduled('tkm_auto_cloes')) {
            wp_schedule_event(time(), 'daily', 'tkm_auto_cloes');
        }

        add_action('tkm_auto_cloes', [$this, 'tkm_auto_cloes_events']);
    }

    public function tkm_auto_cloes_events()
    {
        $active = tkm_settings('cloes_auto_ticket');
        $preoid = tkm_settings('auto_cloes_days');

        if (! $active || $preoid) {
            return NULL;
        }

        global  $wpdb;
        $ticket_table = $wpdb->prefix . 'tkm_tickets';

        $date = date("Y-m-d H:i:s", strtotime("-" . $preoid . ' days', time()));

        $tickets =   $wpdb->get_col("SELECT ID FROM " . $ticket_table . " WHERE status != 'cloesd' AND reply_date < '" . $date . "'");

        if (count($tickets)) {
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
            <p>افزونه تیکت چی برای اجرا صحیح نیاز به نسخه 7.2 به بالا دارد لطفا نسخه php هاست خود را ارتقا دهید
            </p>
        </div>
<?php
    }
}

Core::instance();
