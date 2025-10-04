<?php 

defined('ABSPATH') || exit('NO Access');

class TKM_WC_Dashboard  {

    public function __construct() {
       d3f39699b20b2c4dd150b133079e::e8e7e2a6df3a5f62df84e3587bcc13d($this);
       d3f39699b20b2c4dd150b133079e::cb60397af9eb7a75a3a1db6da5547f($this);
        add_action('woocommerce_account_tickets_endpoint', [$this, 'tickets_endpoint_page']);
    }

    public function ticket_account_menu($items) {
        $logout = null;

        if (isset($items['customer-logout'])) {
            $logout = $items['customer-logout'];
        }

        unset($items['customer-logout']);
        $items['tickets'] = 'تیکت ها  ';

        if ($logout) {
            $items['customer-logout'] = $logout;
        }

        return $items;
    }

    public function add_tickets_endpoint() {
        add_rewrite_endpoint('tickets', EP_PAGES );
        flush_rewrite_rules();
        // Flush rewrite rules only once, for example, on plugin activation
    }

    public function tickets_endpoint_page() {
        include_once $this->get_view();
    }

    public function get_view() {
        if (isset($_GET['action']) && $_GET['action'] == 'new') {
            return TKM_VIEWS_PATH . 'front/new-ticket.php';
        }
        if (isset($_GET['action']) && $_GET['ticket-id'] ) {
            return TKM_VIEWS_PATH . 'front/single-ticket.php';
        }

        return TKM_VIEWS_PATH . 'front/tickets.php';
    }

}