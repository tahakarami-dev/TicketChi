<?php

defined('ABSPATH') || exit('NO Access');

class TKM_ASSETS
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'front_assets'], 999);
        add_action('admin_enqueue_scripts', [$this, 'admin_assets'],);
    }
    public function admin_assets()
    {
        //css
        wp_enqueue_style('tkm-admin-style', TKM_ADMIN_ASSETS . 'css/style.css');
        wp_enqueue_style('tkm-select2', TKM_ADMIN_ASSETS . 'css/select2.min.css');
        wp_enqueue_style('tkm-style_tailwind', 'dist/output.css', '', TKM_VER);


        //script
        wp_enqueue_media();
        wp_enqueue_script('tkm-select2', TKM_ADMIN_ASSETS . 'js/select2.min.js', ['jquery'], '', true);
        wp_enqueue_script('tkm-main', TKM_ADMIN_ASSETS . 'js/main.js', ['jquery'], TKM_VER, true);
        wp_localize_script('tkm-main', 'TKM_DATA', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }

    public function front_assets()
    {
        wp_enqueue_style('tkm-style_front', TKM_FRONT_ASSETS . 'css/style_ticket.css', '', TKM_VER);
        wp_enqueue_style('tkm-style_user_front', TKM_FRONT_ASSETS . 'css/custom-user-style.css', '', TKM_VER);
        wp_enqueue_style('tkm_style_tailwind', '/wp-content/plugins/ticketchi/output.css', '', TKM_VER);
        wp_enqueue_style('tkm-swetalert2-css', TKM_FRONT_ASSETS . 'css/sweetalert2.min.css', '', TKM_VER);

        // scripts
        wp_enqueue_script('tkm-scripts', TKM_FRONT_ASSETS . 'js/scripts.js', ['jquery'], '', true);
        wp_enqueue_script('tkm-swetalert2', TKM_FRONT_ASSETS . 'js/sweetalert2.all.min.js', '', '', true);

        wp_localize_script('tkm-scripts', 'TKM_DATA_AJAX', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tkm_ajax_nonce')
        ]);
    }
}
