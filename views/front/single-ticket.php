<?php
$ticket_id = $_GET['ticket-id'];

$ticket_manager = new TKM_Ticket_Manager();
$ticket = $ticket_manager->get_info_ticket($ticket_id);

$user_data = get_userdata($ticket->creator_id);
$department_manager = new  TKM_Front_Department_Manager();

$reply_manager = new TKM_Reply_Manager($ticket_id);

$replies = $reply_manager->get_replys();

$product_id = $ticket->product ? $ticket->product : NULL; //  شناسه محصول
$product_info = get_product_name_and_link_by_id($product_id);


// گرفتن آیدی تیکت از URL
$ticket_id = isset($_GET['ticket-id']) ? intval($_GET['ticket-id']) : 0;
$rating = $ticket_manager->get_ticket_rating($ticket_id);




$content = ''; // محتوای پیش‌فرض
$editor_id = 'body'; // شناسه یکتا
$settings = array(
  'textarea_name' => 'my_custom_content', // نام برای ذخیره
  'media_buttons' => false, // دکمه‌های آپلود غیرفعال
  'editor_height' => 150, // ارتفاع ادیتور
  'tinymce' => array(
    'toolbar1' => 'bold,italic,underline,|,link,unlink,|,undo,redo', // تنها دکمه‌های انتخابی
    'block_formats' => 'Paragraph=p; Heading 2=h2; Heading 3=h3',
    'valid_elements' => '*[*]', // اجازه به تمام تگ‌ها
    'plugins' => 'link', // تنها پلاگین‌های انتخابی (لینک فعال است)
  ),
  'quicktags' => true, // ابزارک‌های HTML فعال
);




?>

<div class="main_single_ticket">
  <div class="tkm_right_single">
    <p style="font-size: 32px;" class="tkm_title_ticket"><?php echo $ticket->title ?></p>

    </p>
    <div class="response-item-chat">
      <p style="font-weight: 400;" class="body_ticket_text"><?php echo wp_kses_post($ticket->body) ?></p>
      <?php if ($ticket->file): ?>
        <div class="file_box_ticket">
          <a>فایل پیوست :</a>
          <a class="link_file_ticket" href="<?php echo $ticket->file ?>" download="<?php echo $ticket->file ?>"><?php echo tkm_get_file_name($ticket->file)  ?></a>
        </div>
      <?php endif; ?>
      <?php if ($ticket->voice) : ?>
        <div class="voice_reply">
          <audio class="voice_ticket" controls>
            <source src="<?php echo $ticket->voice  ?>" type="audio/wav">
          </audio>
        </div>
      <?php endif; ?>
      <p style="font-size: 14px;" class="timestamp"><?php echo jdate($ticket->create_date) ?></p>

    </div>


    <?php include TKM_VIEWS_PATH . 'front/reply.php' ?>
    <div class="btn_reply_box">
      <a class="byn_send_reply">ارسال پاسخ
        <svg style="margin-left: 5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
      </a>
    </div>



    <div class="tkm_item_send_form">
      <form id="reply-submit" action="#" method="post" enctype="multipart/form-data">
        <input type="hidden" name="ticket_id" value="<?php echo $ticket->ID ?>" id="ticket_id">

        <?php wp_editor($content, $editor_id, $settings); ?>


        <div class="item_cloes">
        </div>
        <div class="items_reply">

          <label for="file-upload" class="file_upload_reply">
            <?php echo  tkm_settings('file_label') ?  tkm_settings('file_label') : 'آپلود فایل '   ?> <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icon_upload">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" />
            </svg>

          </label>
          <input id="file-upload" class="upload_btn_file" type="file" />

          <button type="button" id="openPopup" class="btn_send_voice"> <?php echo  tkm_settings('voice_label') ?  tkm_settings('voice_label') : 'ارسال صدا ' ?>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icon_upload">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z" />
            </svg>
          </button>


        </div>

        <br>
        <div id="popup">
          <h2 class="title_voice">ضبط پیام صوتی</h2>
          <p>لطفا پس از پایان ضبط بر روی دکمه بستن کلیک کنید</p>
          <button type="button" id="startRecording">شروع </button>
          <button type="button" id="stopRecording" style="display: none;">پایان </button>
          <button type="button" id="deleteRecording" style="display: none;">حذف</button>
          <br><br>
          <div id="timerContainer" style="display: none;">
            <span id="timer">00:00</span>
          </div>
          <div class="box_audio">
            <audio id="audioPlayback" controls></audio>

          </div>
          <br>
          <input type="hidden" id="audioData" name="audioData" />
          <br>
          <button type="button" id="closePopup">بستن</button>
        </div>
        <div class="cloes_items">
          <label class="label_cloes" for="status">بستن تیکت</label>
          <input type="checkbox" name="status" id="status" value="cloesd">
        </div>

        <button class="submit-reply" type="submit">ارسال پاسخ <span class="loader" style="display: none;"> در حال ارسال </span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icon_send">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
          </svg></button>
    </div>
  </div>

  <div class="items_left"  style="font-size: 16px;">
    <div class="back_box back_link" style="display: flex; justify-content:left">
      <?php
      $my_account_url = wc_get_page_permalink('myaccount');
      $custome_link = $my_account_url . '/tickets';
      ?>
      <a class="back_link" href="<?php echo esc_url($custome_link) ?>">بازگشت
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
        </svg>
      </a>

      </form>
    </div>
    <div class="tkm_left_single">
      <?php $show_department = $department_manager->get_department($ticket->department_id)  ?>
      <p class="ability_ticket">نام دپارتمان: <span> <?php echo $show_department->name ?></span></p>
      <p class="ability_ticket"> شناسه تیکت: <span><?php echo $ticket->ID ?> </span></p>
      <?php if (tkm_settings('product_setting')  && isset($ticket->product)): ?>
        <p class="ability_ticket"> محصول : <span><?php echo  esc_html($product_info['name']) ?> </span></p>
      <?php endif; ?>

      <p class="ability_ticket"><span> <?php echo get_status_html($ticket->status) ?>
        </span></p>
      <p class="ability_ticket" id="priority_ability">اولویت تیکت: <?php  ?> <span>
          <?php
          switch ($ticket->priority) {
            case 'low':
              echo 'کم';
              break;

            case 'medium':
              echo 'متوسط ';
              break;

            case 'high':
              echo 'زیاد';
              break;
          }
          ?>
        </span></p>
      <p class="ability_ticket">کاربر:‌ <span><?php echo $user_data->display_name ?></span></p>
      <p class="ability_ticket">تاریخ ایجاد: <span><?php echo jdate($ticket->create_date) ?> </span></p>
      <p>تاریخ بروزرسانی: <span><?php echo jdate($ticket->reply_date) ?></span></p>
    </div>
    <?php if (tkm_settings('rating_ticket')): ?>
      <?php if ($ticket_id > 0): ?>
        <?php if ($ticket_manager->has_user_rated_ticket($ticket_id)) : ?>

          <div class="tkm-rating-form">
            <?php if (tkm_settings('rating_ticket_img')): ?>

            <?Php endif ?>
            <p>امتیاز شما به این تیکت</p>
            <div id="rating-container" data-rating="<?php echo esc_attr($rating); ?>">
              <span class="star" data-value="5">&#9733;</span>
              <span class="star" data-value="4">&#9733;</span>
              <span class="star" data-value="3">&#9733;</span>
              <span class="star" data-value="2">&#9733;</span>
              <span class="star" data-value="1">&#9733;</span>
            </div>
          </div>
        <?php else : ?>
          <div class="tkm-rating-form">
            <?php if (tkm_settings('rating_ticket_img')): ?>

            <?Php endif ?> <p class="title_rating"><?php echo tkm_settings('rating_ticket_title') ?></p>
            <div class="tkm-stars" data-rating="0">
              <span class="star" data-value="1">&#9733;</span>
              <span class="star" data-value="2">&#9733;</span>
              <span class="star" data-value="3">&#9733;</span>
              <span class="star" data-value="4">&#9733;</span>
              <span class="star" data-value="5">&#9733;</span>
            </div>
            <button id="tkm-submit-rating" class="tkm-submit-button">ثبت امتیاز</button>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>

  </div>

</div>