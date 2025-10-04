<?php

defined('ABSPATH') || exit('NO Access');

class TKM_Flash_Message{
    const ERROR = 1;
    const SUCCESS = 2;
    const WARNING = 3;
    const INFO = 4;

    public static function add_message($message, $type = self::SUCCESS) {
        if (!isset($_SESSION['tkm']['messages'])) {
            $_SESSION['tkm']['messages'] = [];
        }
        $_SESSION['tkm']['messages'][] = ['body' => $message, 'type' => $type];
    }

    public static function show_message() {
        // شرط درست برای نمایش پیام‌ها
        if (isset($_SESSION['tkm']['messages']) && !empty($_SESSION['tkm']['messages'])) {
            foreach ($_SESSION['tkm']['messages'] as $message) {
                echo '<div class="notice is-dismissible ' . self::get_type($message['type']) . '">';
                echo '<p>';
                echo $message['body'];
                echo '</p>';
                echo '</div>';
            }
            self::empty_session(); // حذف پیام‌ها پس از نمایش
        }
    }

    // تابع برای تعیین نوع پیام
    public static function get_type($type) {
        switch ($type) {
            case self::SUCCESS:
                return 'notice-success';
            case self::ERROR:
                return 'notice-error';
            case self::WARNING:
                return 'notice-warning';
            case self::INFO:
                return 'notice-info';
            default:
                return 'notice-info';
        }
    }

    // تابع برای خالی کردن سشن پیام‌ها
    public static function empty_session() {
        unset($_SESSION['tkm']['messages']);
    }
}