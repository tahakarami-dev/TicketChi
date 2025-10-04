<?php

function tkm_user_meta_key()
{
  global $wpdb;
  $fields = $wpdb->get_col("SELECT DISTINCT meta_key FROM {$wpdb->usermeta} ");
  $array = [];

  foreach ($fields as $field) {
    $array[$field] = $field;
  }


  return $array;

  // دریافت کد CSS از گزینه ذخیره‌شده در Codestar Framework
  $custom_css = tkm_settings('css_costume'); // نام گزینه ذخیره‌شده را تغییر دهید


  // مسیر فایل CSS که می‌خواهید ایجاد کنید
  $file = TKM_FRONT_ASSETS . 'css/custom-user-style.css';
  // ذخیره کد CSS در فایل custom-style.css
  file_put_contents($file, $custom_css);
}

// Control core classes for avoid errors
if (class_exists('CSF')) {

  //
  // Set a unique slug-like ID
  $prefix = 'tkm_settings';

  //
  // Create options
  CSF::createOptions($prefix, array(
    'menu_title' => 'تیکت چی',
    'menu_slug'  => 'tkm-settings',
    'menu_hidden' => true,
    'framework_title' => 'تیکت چی',
    'theme' => 'light',
    'footer_text' => '',
    'class' => 'ticket_master',
    'sticky_header' => false,
    'show_search' => false
  ));


  // Create a section
  CSF::createSection($prefix, array(
    'title'  => 'عمومی',
    'fields' => array(

      // add field
      array(
        'id'      => 'new-ticket-alert',
        'type'    => 'switcher',
        'title'   => 'پیغام ارسال تیکت',
        'label'   => 'نمایش پیغام قبل از ثبت تیکت',
        'default' => false
      ),
      array(
        'id'      => 'alert-message-ticket',
        'type'    => 'textarea',
        'title'   => 'متن پیغام',
        'default' => 'کاربر گرامی قبل از ثبت تیکت حتما سوالات متداول را مطالعه بفرمایید',
        'dependency' => array('new-ticket-alert', '==', 'true')
      ),
      array(
        'id'      => 'cloes_auto_ticket',
        'type'    => 'switcher',
        'title'   => 'فعال سازی',
        'label'   => 'بستن خودکار تیکت ها  ',
        'default' => false
      ),
      array(
        'id'    => 'auto_cloes_days',
        'type'  => 'text',
        'title' => ' مدت زمان بستن تیکت (روز)'
      ),
      array(
        'id'      => 'rating_ticket',
        'type'    => 'switcher',
        'title'   => 'فعال سازی',
        'label'   => 'امتیاز دهی به تیکت ',
        'default' => true
      ),
      array(
        'id'    => 'rating_ticket_title',
        'type'  => 'text',
        'title' => 'عنوان امتیاز دهی به تیکت ',
        'default' => 'لطفا امتیاز تیکت را ثبت نمایید',
        'dependency' => array('rating_ticket', '==', 'true')

      ),


    )
  ));

  CSF::createSection( $prefix, array(
    'title'  => 'استایل',
    'id'     => 'tkm_style',
    'fields' => array(
        array(
            'id'      => 'primary_color',
            'type'    => 'color',
            'title'   => 'رنگ اصلی',
            'default' => '#2A48CD',
      'output'=>array(
          'background-color'=>'.new-ticket-btn, #tkm-filter-btn,.btn-eye,.btn_show_tickets,.custom-file-upload,.tkm-submit-ticket,.byn_send_reply,.submit-reply,.file_upload_reply,.btn_send_voice',
        ),
        'output_important'=> true,

  
        ),
        array(
            'id'      => 'secondary_color',
            'type'    => 'color',
            'title'   => 'رنگ ثانویه',
            'default' => '#2947cc8a',

            'output'=>array(
              'background-color'=>'new-ticket-btn:hover, #tkm-filter-btn:hover, .btn-eye:hover, .btn_show_tickets:hover, .custom-file-upload:hover, .tkm-submit-ticket:hover, .byn_send_reply:hover, .submit-reply:hover, .file_upload_reply:hover, .btn_send_voice:hover,.divider hr',
               'border-color'=>'.response-item-chat',
               'color'=>'.link-title:hover,.back_link:hover'
            ),
            'output_important'=> true,
    
      
        ),
        array(
            'id'      => 'background_color',
            'type'    => 'color',
            'title'   => 'پس زمینه',
            'default' => '#d3eef2',

            'output'=>array(
              'background-color'=>'.file_box,.tkm_left_single,.items_reply,.items_reply:hover',
            ),
            'output_important'=> true,
        ),
        array(
            'id'      => 'text_color',
            'type'    => 'color',
            'title'   => 'رنگ متن',
            'default' => '#ffffff',

            'output'=>array(
              'color'=>'.new-ticket-btn, #tkm-filter-btn,.btn-eye,.btn_show_tickets,.custom-file-upload,.tkm-submit-ticket,.byn_send_reply,.submit-reply,.file_upload_reply,.btn_send_voice,new-ticket-btn:hover, #tkm-filter-btn:hover, .btn-eye:hover, .btn_show_tickets:hover, .custom-file-upload:hover, .tkm-submit-ticket:hover, .byn_send_reply:hover, .submit-reply:hover, .file_upload_reply:hover, .btn_send_voice:hover',
            ),
            'output_important'=> true,
        ),
    )
));

  CSF::createSection($prefix, array(
    'title'  => 'فیلدها',
    'fields' => array(

        array(
            'id'      => 'title_dashboard',
            'type'    => 'text',
            'title'   => 'عنوان داشبورد',
            'default' => 'داشبورد تیکت‌ها',
        ),

        array(
            'id'      => 'description_dashboard',
            'type'    => 'text',
            'title'   => 'توضیحات داشبورد',
            'default' => 'مشاهده، ویرایش و مدیریت تیکت‌های دریافت شده و ارسال شده',
        ),
        array(
          'id'      => 'department_label',
          'type'    => 'text',
          'title'   => 'فیلد دپارتمان',
          'default' => 'دپارتمان',
      ),
      array(
        'id'      => 'type_label',
        'type'    => 'text',
        'title'   => 'فیلد نوع تیکت',
        'default' => 'نوع تیکت',
    ),
    array(
      'id'      => 'title_label',
      'type'    => 'text',
      'title'   => 'فیلد عنوان تیکت',
      'default' => ' عنوان تیکت',
  ),
  array(
    'id'      => 'product_label',
    'type'    => 'text',
    'title'   => 'فیلد محصول  ',
    'default' => ' محصول ',
),
array(
  'id'      => 'priority_label',
  'type'    => 'text',
  'title'   => 'فیلد اهمیت تیکت  ',
  'default' => ' اهمیت تیکت ',
),
array(
  'id'      => 'dec_label',
  'type'    => 'text',
  'title'   => 'فیلد  توضیحات  ',
  'default' => '  توضیحات ',
),
array(
  'id'      => 'file_label',
  'type'    => 'text',
  'title'   => 'فیلد  آپلود فایل   ',
  'default' => '  آپلود فایل  ',
),array(
  'id'      => 'voice_label',
  'type'    => 'text',
  'title'   => 'فیلد  ارسال پیام صوتی   ',
  'default' => '  ارسال صدا  ',
),

    ),
));

  CSF::createSection($prefix, array(
    'title'  => 'پاسخ های آماده',
    'fields' => array(
      array(
        'id'     => 'save_message',
        'type'   => 'repeater',
        'title'  => 'پاسخ های آماده ',
        'fields' => array(

          array(
            'id'    => 'save_message_title',
            'type'  => 'text',
            'title' => 'شماره پاسخ'
          ),
          array(
            'id'    => 'message_save',
            'type'  => 'textarea',
            'title' => ' متن پاسخ آماده'
          ),

        ),
      ),



    )
  ));

  CSF::createSection($prefix, array(
    'title'  => 'سوالات متداول',
    'fields' => array(

      // add field
      array(
        'id'      => 'faq_swicher',
        'type'    => 'switcher',
        'title'   => 'سوالات متداول ',
        'label'   => 'آیا سوالات متداول قبل از ثبت تیکت نمایش داده شود ',
        'default' => false
      ),
      array(
        'id'     => 'faqs',
        'type'   => 'repeater',
        'title'  => 'سوال جدید',
        'fields' => array(

          array(
            'id'    => 'faqs-title',
            'type'  => 'text',
            'title' => 'عنوان سوال'
          ),
          array(
            'id'    => 'faqs-body',
            'type'  => 'textarea',
            'title' => 'توضیح سوال'
          ),

        ),
      ),

    )
  ));

  CSF::createSection($prefix, array(
    'title'  => ' وضعیت ها',
    'fields' => array(

      // add field
      array(
        'id'      => 'open-color',
        'type'    => 'color',
        'title'   => 'رنگ وضعیت باز',
        'label'   => 'رنگ نمایش وضعیت ',
        'default' => '#4bcc58'
      ),
      array(
        'id'      => 'cloes-color',
        'type'    => 'color',
        'title'   => 'رنگ وضعیت پاسخ داده شده',
        'label'   => 'رنگ نمایش وضعیت ',
        'default' => '#3150d8'
      ),
      array(
        'id'      => 'answerd-color',
        'type'    => 'color',
        'title'   => 'رنگ وضعیت بسته شده ',
        'label'   => 'رنگ نمایش وضعیت ',
        'default' => '#ff2121'
      ),
      array(
        'id'      => 'finish-color',
        'type'    => 'color',
        'title'   => 'رنگ وضعیت پایان یافته',
        'label'   => 'رنگ نمایش وضعیت ',
        'default' => '#000000'
      ),
      array(
        'id'      => 'trash-color',
        'type'    => 'color',
        'title'   => 'رنگ وضعیت زباله دان ',
        'label'   => 'رنگ نمایش وضعیت ',
        'default' => '#000000'
      ),
      array(
        'id'     => 'statues',
        'type'   => 'repeater',
        'title'  => 'وضعیت جدید ',
        'fields' => array(

          array(
            'id'    => 'status-title',
            'type'  => 'text',
            'title' => 'نام وضعیت '
          ),
          array(
            'id'    => 'status-slug',
            'type'  => 'text',
            'title' => 'نامک  '
          ),
          array(
            'id'    => 'color-status',
            'type'  => 'color',
            'title' => 'رنگ وضعیت '
          ),

        ),
      ),





    )
  ));

  CSF::createSection($prefix, array(
    'title' => 'پیامک',
    'id' => 'sms_section'



  ));

  CSF::createSection($prefix, array(
    'parent' => 'sms_section',
    'title'  => 'تنظیمات سامانه',
    'fields' => array(

      // add field
      array(
        'id'    => 'sms-service',
        'type'  => 'select',
        'title' => 'سامانه های پیامکی',
        'options' => array(
          'melipayamak' => "ملی پیامک",


        ),
      ),

      array(
        'id'    => 'phone-service-key-user',
        'type'  => 'select',
        'title' => '  کلید شماره موبایل ',
        'options' => 'tkm_user_meta_key'


      ),

      array(
        'id'    => 'sms-username',
        'type'  => 'text',
        'title' => 'نام کابری سامانه  ',
        'dependency' => array('sms-service', '==', 'melipayamak'),


      ),
      array(
        'id'    => 'sms-password',
        'type'  => 'text',
        'title' => ' رمز عبور سامانه ',
        'dependency' => array('sms-service', '==', 'melipayamak'),


      ),
      array(
        'id'    => 'sms-api',
        'type'  => 'text',
        'title' => ' کد api ',
        'dependency' => array('sms-service', '==', 'kavehnegar'),


      ),
    )
  ));

  CSF::createSection($prefix, array(
    'parent' => 'sms_section',
    'title' => 'ارسال تیکت',
    'id' => 'sms_section',
    'fields' => array(



      array(
        'id'      => 'user_create_sms',
        'type'    => 'switcher',
        'title'   => 'فعال سازی',
        'label'   => ' ارسال پیامک هنگام ایجاد تیکت',
        'default' => false
      ),
      array(
        'id'      => 'user_create_sms_pattern_code',
        'type'    => 'text',
        'title'   => 'کد الگو',
        'default' => false
      ),
      array(
        'id'      => 'user_create_pattern',
        'type'    => 'textarea',
        'title'   => 'الگو',
        'default' => false
      ),
      array(
        'id'      => 'user_create_pattern',
        'type'    => 'content',
        'content' => '<p>شناسه تیکت : {{ticket_id}}</p>' .
          '<p>عنوان تیکت : {{title}}</p>' .
          '<p>دپارتمان تیکت : {{department}}</p>' .
          '<p> وضعیت تیکت : {{status}}</p>' .
          '<p>اهمیت تیکت : {{priority}}</p>' .
          '<p>تاریخ تیکت : {{date}}</p>',


      ),


    )




  ));


  // CSF::createSection($prefix, array(
  //   'title' => 'ایمیل',
  //   'id' => 'email_section'



  // ));

  // CSF::createSection($prefix, array(
  //   'parent' => 'email_section',
  //   'title'  => 'تنظیمات ایمیل',
  //   'fields' => array(

  //     // add field
  //     array(
  //       'id'    => 'email-from',
  //       'type'  => 'text',
  //       'title' => '  ایمیل ارسال کننده',
  //     ),
  //     array(
  //       'id'    => 'email-sender',
  //       'type'  => 'text',
  //       'title' => '  نام ارسال کننده',
  //     ),

  //   )
  // ));

  // CSF::createSection($prefix, array(
  //   'parent' => 'email_section',
  //   'title' => 'ارسال ایمیل',
  //   'id' => 'sms_section',
  //   'fields' => array(



  //     array(
  //       'id'      => 'user_create_email',
  //       'type'    => 'switcher',
  //       'title'   => 'فعال سازی',
  //       'label'   => ' ارسال ایمیل هنگام ایجاد تیکت',
  //       'default' => false
  //     ),

  //     array(
  //       'id'      => 'pattern_email',
  //       'type'    => 'wp_editor',
  //       'title'   => 'متن ایمیل',
  //       'default' => false
  //     ),
  //     array(
  //       'id'      => 'user_create_pattern',
  //       'type'    => 'content',
  //       'content' => '<p>شناسه تیکت : {{ticket_id}}</p>' .
  //         '<p>عنوان تیکت : {{title}}</p>' .
  //         '<p>دپارتمان تیکت : {{department}}</p>' .
  //         '<p> وضعیت تیکت : {{status}}</p>' .
  //         '<p>اهمیت تیکت : {{priority}}</p>' .
  //         '<p>تاریخ تیکت : {{date}}</p>',


  //     ),


  //   )




  // ));

  CSF::createSection($prefix, array(
    'title'  => 'ووکامرس',
    'fields' => array(
      array(
        'id'      => 'product_setting',
        'type'    => 'switcher',
        'title'   => ' پشیبانی محصولات ووکامرس ',
        'label'   => '  آیا می‌خواهید کاربر برای محصولات ووکامرس خریداری شده تیکت ثبت نماید  ',
        'default' => false
      ),



    )
  ));

  CSF::createSection($prefix, array(
    'title'  => 'پاسخ هوشمند ',
    'fields' => array(

      // add field
      array(
        'id'      => 'auto_reply',
        'type'    => 'switcher',
        'title'   => ' پاسخ هوشمند ',
        'label'   => 'این پاسخ بعد از ثبت تیکت توسط کاربر نمایش داده میشود ',
        'default' => false
      ),
      array(
        'id'      => 'auto_reply_text',
        'type'    => 'textarea',
        'title'   => ' متن پاسخ هوشمند ',
        'placeholder'   => ' این متن در پاسخ هوشمند نمایش داده خواهد شد ',
        'dependency' => array('auto_reply', '==', 'true')

      ),
   
      

    )
  ));
}
