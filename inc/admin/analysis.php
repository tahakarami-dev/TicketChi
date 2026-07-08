<?php

defined('ABSPATH') || exit('NO Access');

class TKM_Analysis
{

   public function count_tickets()
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'tkm_tickets';

      if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
         return 0; 
      }

      $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name");
      $total_tickets = $wpdb->get_var($query);

      return (int) $total_tickets; 
   }

   public function count_replys()
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'tkm_replies';

      if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
         return 0; 
      }

      $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name");
      $total_tickets = $wpdb->get_var($query);

      return (int) $total_tickets; 
   }

   public function get_average_response_time()
   {
      global $wpdb;
      $table_name = $wpdb->prefix . 'tkm_tickets'; 

      $query = "SELECT AVG(UNIX_TIMESTAMP(reply_date) - UNIX_TIMESTAMP(create_date)) AS avg_time FROM $table_name";
      $result = $wpdb->get_var($query);

      if ($result === null) {
         return 'بدون داده';
      }

      return gmdate("H:i:s", $result); 
   }
   public function get_average_ticket_rating()
   {
      global $wpdb;
      $table_name = $wpdb->prefix . 'tkm_ratings'; 

      $query = "SELECT AVG(rating) AS avg_rating FROM $table_name";
      $result = $wpdb->get_var($query);

      if ($result === null) {
         return 'بدون امتیاز';
      }

      return number_format($result, 2); 
   }
   public function count_users()
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'tkm_users';

      if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
         return 0; 
      }

      $query = $wpdb->prepare("SELECT COUNT(DISTINCT user_id) FROM $table_name");
      $total_users = $wpdb->get_var($query);

      return (int) $total_users; 
   }


   public function get_user_satisfaction_description()
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'tkm_ratings';

      if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
         return 'جدول کاربران موجود نیست.';
      }

      $query = $wpdb->prepare("SELECT AVG(rating) FROM $table_name WHERE rating IS NOT NULL");
      $average_rating = $wpdb->get_var($query);

      if ($average_rating === null) {
         return 'نامشخص';
      }

      if ($average_rating >= 4.5) {
         return 'عالی';
      } elseif ($average_rating >= 3.5) {
         return 'خوب';
      } elseif ($average_rating >= 2.5) {
         return 'متوسط';
      } else {
         return 'ناراضی';
      }
   }
   public function get_employee_ticket_stats()
   {
      global $wpdb;

      $results = $wpdb->get_results("
          SELECT 
              u.user_id,
              u.department_id,
              COUNT(t.ID) AS ticket_count,  -- تعداد کل تیکت‌ها
              COUNT(CASE WHEN t.status = 'open' THEN 1 END) AS open_ticket_count,  -- تعداد تیکت‌های باز
              COUNT(CASE WHEN t.status = 'answerd' THEN 1 END) AS answered_ticket_count,  -- تعداد تیکت‌های پاسخ داده شده
              ROUND(AVG(r.rating), 2) AS avg_rating  -- میانگین امتیاز تیکت‌ها
          FROM {$wpdb->prefix}tkm_users u
          LEFT JOIN {$wpdb->prefix}tkm_tickets t ON u.department_id = t.department_id
          LEFT JOIN {$wpdb->prefix}tkm_ratings r ON t.ID = r.ticket_id
          GROUP BY u.user_id
      ");

      return $results;
   }
}
