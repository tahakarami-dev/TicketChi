<?php

$ticket_id = $_GET['ticket-id'];
$ticket_manager = new TKM_Ticket_Manager();
$ticket = $ticket_manager->get_info_ticket($ticket_id);

$user_data = get_userdata($ticket->creator_id);
$department_manager = new  TKM_Front_Department_Manager();

$reply_manager = new TKM_Reply_Manager($ticket_id);
$replies = $reply_manager->get_replys();

$product_id = $ticket->product ? $ticket->product : NULL;
$product_info = get_product_name_and_link_by_id($product_id);

if (tkm_settings('edd_setting') == true) {
  $edd_product_id = $ticket->edd_product ? $ticket->edd_product : NULL;
  $edd_product_info = get_the_title($edd_product_id);
}

$ticket_id = isset($_GET['ticket-id']) ? intval($_GET['ticket-id']) : 0;
$rating = $ticket_manager->get_ticket_rating($ticket_id);

$content = '';
$editor_id = 'body';
$settings = array(
  'textarea_name' => 'my_custom_content',
  'media_buttons' => false,
  'editor_height' => 150,
  'tinymce' => array(
    'toolbar1' => 'bold,italic,underline,|,link,unlink,|,undo,redo',
    'block_formats' => 'Paragraph=p; Heading 2=h2; Heading 3=h3',
    'valid_elements' => '*[*]',
    'plugins' => 'link',
  ),
  'quicktags' => true,
);

$is_shortcode = tkm_settings('shortcode');
?>
<?php if (is_user_logged_in()): ?>
  <div class="tkm-shortcode-wrapper">
    <div class="main_single_ticket">
      <div class="tkm_right_single">
        <h2 style="font-weight: 700 !important;" class="tkm_title_ticket"><?php echo $ticket->title ?></h2>
        </p>
        <div class="response-item-chat">
          <span class="body_ticket_text"><?php echo wp_kses_post($ticket->body) ?></span>
          <?php $ticket_files = tkm_get_files($ticket->file); ?>
          <?php if (!empty($ticket_files)): ?>
            <div class="file_box_ticket tkm-files-list">
              <?php foreach ($ticket_files as $ticket_file): ?>
                <a class="link_file_ticket" href="<?php echo esc_url($ticket_file) ?>" download="<?php echo esc_attr($ticket_file) ?>"> فایل پیوست: <?php echo esc_html(tkm_get_file_name($ticket_file)) ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php if ($ticket->voice) : ?>
            <div class="voice_reply">
              <audio class="voice_ticket" controls>
                <source src="<?php echo $ticket->voice  ?>" type="audio/wav">
              </audio>
            </div>
          <?php endif; ?>
          <p style="font-size: 14px;" class="timestamp"><?php echo wp_date( get_option('date_format') . ' ' . get_option('time_format'), strtotime($ticket->create_date) );?></p>
        </div>
        <?php include TKM_VIEWS_PATH . 'front/reply.php' ?>
        <div class="btn_reply_box">
          <a class="byn_send_reply"> پاسخ به تیکت
            <img width="18px" style="height: 18px;" src="<?php echo TKM_FRONT_ASSETS . '/images/plus.png' ?>" alt="">
          </a>
        </div>
        <div class="tkm_item_send_form">
          <form id="reply-submit" action="#" method="post" enctype="multipart/form-data">
            <input type="hidden" name="ticket_id" value="<?php echo $ticket->ID ?>" id="ticket_id">
            <?php wp_editor($content, $editor_id, $settings); ?>
            <div class="item_close">
            </div>
            <div class="reply-attachment">
              <label for="file-upload" class="attachment-file" style="margin: 0;">
                <?php echo  tkm_settings('file_label') ?  tkm_settings('file_label') : 'آپلود فایل '   ?>
                <img width="18px" style="height: 18px;" src="<?php echo TKM_FRONT_ASSETS . '/images/upload.png' ?>" alt="">
              </label>
              <input id="file-upload" class="upload_btn_file" type="file" <?php echo tkm_settings('upload_multiple') ? 'multiple' : '' ?> accept="<?php echo esc_attr(tkm_get_accept_attr()) ?>" />
              <div class="tkm-selected-files" id="reply-selected-files"></div>

              <button type="button" id="openPopup" class="attachment-file"> <?php echo  tkm_settings('voice_label') ?  tkm_settings('voice_label') : 'ارسال صدا ' ?>
                <img width="18px" style="height: 18px;" src="<?php echo TKM_FRONT_ASSETS . '/images/microphone.png' ?>" alt="">
              </button>
            </div>
            <br>
            <div id="popup">
              <h2 class="popup-title">ضبط پیام صوتی</h2>
              <p>لطفا پس از پایان ضبط بر روی دکمه بستن کلیک کنید</p>
              <button class="recording-btn" type="button" id="startRecording">شروع</button>
              <button class="recording-btn" type="button" id="stopRecording" style="display: none;">پایان </button>
              <button class="recording-btn" type="button" id="deleteRecording" style="display: none;">حذف</button>
              <br><br>
              <div id="timerContainer" style="display: none;">
                <span class="timer" id="timer">00:00</span>
              </div>
              <div class="box_audio" style="margin-top: 20px;">
                <audio id="audioPlayback" controls></audio>
              </div>
              <br>
              <input type="hidden" id="audioData" name="audioData" />
              <br>
              <button class="recording-btn" type="button" id="closePopup">بستن</button>
            </div>
            <div class="cloes-item">
              <label class="" for="status">بستن تیکت</label>
              <input class="cloes-checkbox" type="checkbox" name="status" id="status" value="closed">
            </div>

            <button class="submit-reply" type="submit">ارسال پاسخ <span class="loader" style="display: none;"> در حال ارسال </span>
              <img width="18px" style="height: 18px;" src="<?php echo TKM_FRONT_ASSETS . '/images/send.png' ?>" alt="">
            </button>
        </div>
      </div>

      <div class="items_left" style="font-size: 16px;">
        <div class="back_box back_link" style="display: flex; justify-content:left">
          <a class="back-link" href="<?php echo $is_shortcode ? TKM_Shortcode_Url::base() : TKM_Ticket_Url::all() ?>">بازگشت
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

          <?php if (tkm_settings('edd_setting')): ?>
            <p class="ability_ticket"> محصول دیجیتال : <span><?php echo  esc_html($edd_product_info) ?> </span></p>
          <?php endif; ?>
          <p class="ability_ticket"><span> <?php echo get_status_html($ticket->status) ?></span></p>
          <p class="ability_ticket" id="priority_ability">اولویت تیکت:<span>
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
          <p class="ability_ticket">تاریخ ایجاد: <span><?php echo wp_date( get_option('date_format') . ' ' . get_option('time_format'), strtotime($ticket->create_date) ); ?> </span></p>
          <p>تاریخ بروزرسانی: <span><?php  echo wp_date( get_option('date_format') . ' ' . get_option('time_format'), strtotime($ticket->reply_date) ); ?></span></p>
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
  </div>
<?php endif; ?>