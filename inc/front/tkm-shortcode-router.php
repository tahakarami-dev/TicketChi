<?php

defined('ABSPATH') || exit;

class TKM_Shortcode_Router {

    public function __construct() {
        add_shortcode('tickets', [$this, 'render_shortcode']);
    }

    public function render_shortcode($atts) {

        ob_start();

        $action    = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : null;
        $ticket_id = isset($_GET['ticket-id']) ? intval($_GET['ticket-id']) : null;

        switch ($action) {

            case 'new':
                $this->load_view('new-ticket');
                break;

            case 'single':
                if ($ticket_id) {
                    $this->load_view('single-ticket');
                } else {
                    $this->load_view('tickets');
                }
                break;

            default:
                $this->load_view('tickets');
                break;
        }

        return ob_get_clean();
    }

    private function load_view($name) {
        $file = TKM_VIEWS_PATH . 'front/' . $name . '.php';

        if (file_exists($file)) {
            include $file;
        } else {
            echo "<p>View not found: {$name}</p>";
        }
    }
}

