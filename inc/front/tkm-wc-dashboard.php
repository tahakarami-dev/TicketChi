<?php

defined('ABSPATH') || exit('NO Access');

class TKM_WC_Dashboard
{

    public function __construct()
    {
        add_action('init', [$this, 'add_tickets_endpoint']);
        add_filter('woocommerce_account_menu_items', [$this, 'ticket_account_menu']);
        add_action('woocommerce_account_tickets_endpoint', [$this, 'tickets_endpoint_page']);
    }

    public function ticket_account_menu($items)
    {
        $logout = null;

        if (isset($items['customer-logout'])) {
            $logout = $items['customer-logout'];
        }

        unset($items['customer-logout']);
        $items['tickets'] = 'تیکت های پشتیبانی';

        if ($logout) {
            $items['customer-logout'] = $logout;
        }

        return $items;
    }

    public function add_tickets_endpoint()
    {
        add_rewrite_endpoint('tickets', EP_PAGES);
        flush_rewrite_rules();
    }

    public function tickets_endpoint_page()
    {
        include_once $this->get_view();
    }

    public function get_view()
    {
        if (isset($_GET['action']) && $_GET['action'] == 'new') {
            return TKM_VIEWS_PATH . 'front/new-ticket.php';
        }
        if (isset($_GET['action']) && $_GET['ticket-id']) {
            return TKM_VIEWS_PATH . 'front/single-ticket.php';
        }

        return TKM_VIEWS_PATH . 'front/tickets.php';
    }
}
