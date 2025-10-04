<?php

function tkm_settings($key = '')
{
    $options = get_option('tkm_settings');
    return isset($options[$key]) ? $options[$key] : null;
}



function tkm_get_status()
{
  $open_color =  tkm_settings('open-color');
  $cloes_color =  tkm_settings('cloes-color');
  $answerd_color =  tkm_settings('answerd-color');
  $finish_color =  tkm_settings('finish-color');

  $statues = tkm_settings('statues');

    $status_array = [

        ['slug' => 'open', 'name' => 'باز', 'color' => $open_color],
        ['slug' => 'answerd', 'name' => 'پاسخ داده ', 'color' => $cloes_color],
        ['slug' => 'cloesd', 'name' => 'بسته شده', 'color' => $answerd_color],
        ['slug' => 'finish', 'name' => 'پایان یافته', 'color' => $finish_color]
    ];
    if(is_array($statues)){
        foreach($statues as $status){
            $status_array[]=[
                'slug'=> $status['status-slug'],
                'name'=> $status['status-title'],
                'color'=> $status['color-status']
            ];

        }
    }

if(is_admin()){
    $status_array[] =
        ['slug' => 'trash', 'name' => 'زباله دان ', 'color' => '#000000']
    ;
}

    return $status_array;
}

function get_status_color($status)
{
    $statuses = tkm_get_status();
    foreach ($statuses as $item) {
        if ($status == $item['slug']) {
            return $item['color'];
        }
    }
    return '#000000'; // رنگ پیش‌فرض در صورت عدم تطابق
}

function tkm_get_file_name($url)
{

    $path =   parse_url($url, PHP_URL_PATH);
    return basename($path);
}

function get_status_name($status)
{
    $statuses = tkm_get_status();
    foreach ($statuses as $item) {
        if ($status == $item['slug']) {
            return $item['name'];
        }
    }
}

function get_status_html($status)
{

    $status_name = get_status_name($status);
    $status_color = get_status_color($status);

   $style_status = is_admin() &&  !wp_doing_ajax() ? 'style="background:' . $status_color .'"' : '';


    return '  <div class="status-ticket '.$style_status.'">
            <p>وضعیت تیکت:‌ <span class="name-status" style=" color: ' . $status_color . '">' . $status_name . '</span></p>

            </div>';
}

function get_department_html($department_id){
   $department_manager =  new TKM_Front_Department_Manager();
 $department =  $department_manager->get_department($department_id);
 
 return '<span?>'.esc_html( $department->name ).'</span>';

}

function get_priority_name($priority){

    switch($priority){
        case 'low' : 
            return 'کم' ;
            break;

            case 'medium' : 
                return 'متوسط' ;
                break;

                case 'high' : 
                    return 'زیاد' ;
                    break;
    }
}
function convert_to_persian_numbers($number) {
    $farsi_numbers = [
        '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
        '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹'
    ];

    // تبدیل هر رقم انگلیسی به معادل فارسی
    return strtr($number, $farsi_numbers);
}
function format_date($timestamp){
jdate($timestamp)->format("Y-m-d H:i");
}
function get_product_name_and_link_by_id($product_id) {
    // دریافت آبجکت محصول
    $product = wc_get_product($product_id);

    // بررسی موجودیت محصول
    if (!$product) {
        return [
            'name' => 'محصول پیدا نشد',
            'link' => null,
        ];
    }

    // دریافت نام محصول
    $product_name = $product->get_name();

    // دریافت لینک محصول
    $product_link = get_permalink($product_id);

    // برگرداندن آرایه شامل نام و لینک
    return [
        'name' => $product_name,
        'link' => $product_link,
    ];
}

function tkm_get_user_tickets_by_department($user_id = null) {
    global $wpdb;

    // اگر کاربر وارد نشده باشد
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return [];
    }

    // کوئری برای دریافت تیکت‌های مرتبط با دپارتمان کاربر
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT t.* 
             FROM {$wpdb->prefix}tkm_tickets t
             INNER JOIN {$wpdb->prefix}tkm_departments d ON t.department_id = d.id
             INNER JOIN {$wpdb->prefix}tkm_users u ON u.department_id = d.id
             WHERE u.user_id = %d",
            $user_id
        ),
        ARRAY_A // بازگشت به صورت آرایه
    );

    return $results;
}
