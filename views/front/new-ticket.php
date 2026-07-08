<?php

$department_manage = new TKM_Front_Department_Manager();
$parent_departments =  $department_manage->get_parent_department();
$ticket_manager = new TKM_Ticket_Manager;

$content = '';
$editor_id = 'ticket-content';
$settings = array(
  'textarea_name' => 'my_custom_content',
  'media_buttons' => false,
  'editor_height' => 300,
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
    <div class="container-ticket">

      <div class="new-ticket-header">
        <a class="btn_show_tickets" href="<?php echo $is_shortcode ? TKM_Shortcode_Url::base() : TKM_Ticket_Url::all(); ?>">همه تیکت ها
        </a>
        <h2>ایجاد تیکت جدید</h2>
      </div>

      <?php if (tkm_settings('new-ticket-alert')) : ?>
        <div class="info-alert">
          <p class="alert-message"><?php echo tkm_settings('alert-message-ticket'); ?></p>
        </div>
      <?php endif; ?>

      <?php if (tkm_settings('faq_swicher') == 1): ?>
        <div class="faq_sec">

          <h4 class="faqs">سوالات متداول</h4>

          <?php foreach (tkm_settings('faqs') as $faq): ?>
            <div class="accordion">
              <p class="title_faq"><?php echo esc_html($faq['faqs-title']); ?></p>
              <img width="15px" style="height: 15px;" src="<?php echo TKM_FRONT_ASSETS . '/images/plus.png' ?>" alt="">
            </div>
            <div class="accordion-content">
              <p class="body_faq"><?php echo trim($faq['faqs-body']); ?></p>
            </div>

          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="sec-not-faqs">
        <a class="btn-not-faqs">جواب سوالم را پیدا نکردم!</a>
      </div>
      <?php if (isset($parent_departments) && count($parent_departments) > 0): ?>
        <div class="main_content_form" style="display: none;">
          <div class="form_container">
            <form id="tkm-submit-ticket" action="" method="POST">
              <div class="sec1">
                <div class="sec_right"></div>
                <div class="form-group">
                  <label class="tkm_label" for="depaertment"><?php echo tkm_settings('department_label') ? tkm_settings('department_label') : 'دپارتمان'  ?></label>
                  <select class="select1" name="tkm-parent-depaertment" id="tkm-parent-depaertment">
                    <option value="">یک مورد را انتخاب کنید</option>
                    <?php foreach ($parent_departments as $item): ?>
                      <option value="<?php echo esc_attr($item->ID); ?>"><?php echo esc_html($item->name); ?></option>
                    <?php endforeach; ?>
                  </select>


                </div>
              </div>
              <div class="sec_left">

                <div class="form-group">
                  <label class="tkm_label" for="tkm-child-department"><?php echo tkm_settings('type_label') ? tkm_settings('type_label') : 'نوع تیکت '  ?> </label>
                  <select class="tkm-child-department select1" name="tkm-child-department" id="tkm-child-department">
                    <option value="">یک مورد را انتخاب کنید</option>
                    <!-- زیرمنوهای فرزند مرتبط با هر والد -->
                    <?php foreach ($parent_departments as $parent_department): ?>
                      <?php $childs = $department_manage->get_child_department($parent_department->ID); ?>
                      <?php foreach ($childs as $child): ?>
                        <option class="child-department child-department-<?php echo esc_attr($parent_department->ID); ?>" value="<?php echo esc_attr($child->ID); ?>" style="display: none;">
                          <?php echo esc_html($child->name); ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endforeach; ?>
                  </select>
                  <!-- نمایش توضیحات مربوط به دپارتمان‌های فرزند -->
                  <?php foreach ($parent_departments as $parent_department): ?>
                    <?php $childs = $department_manage->get_child_department($parent_department->ID); ?>
                    <?php foreach ($childs as $child): ?>
                      <?php if ($child->description !== ''): ?>
                        <div class="alert-department alert-department-<?php echo $child->ID; ?>" style="display: none;">
                          <p class="message_alert"><?php echo trim(esc_html($child->description)) ?></p>
                        </div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php endforeach; ?>
                  <div class="sec2">
                    <label class="tkm_label_2" for=""> <?php echo tkm_settings('title_label') ? tkm_settings('title_label') : 'عنوان تیکت'  ?></label>
                    <input class="tkm_input_form title_new_ticket" type="text" id="title-ticket" name="title_ticket">
                    <?php if (tkm_settings('product_setting')): ?>
                      <?php

                      $current_user_id = get_current_user_id();
                      $purchased_products = $ticket_manager->get_user_purchased_products($current_user_id);

                      if (!empty($purchased_products)): ?>
                        <label class="tkm_label priority_label" for="user_purchased_products"> <?php echo tkm_settings('product_label') ?  tkm_settings('product_label') : 'محصول'  ?></label>
                        <select name="user_purchased_products" class="tkm_input_form" id="products">
                          <option value="">لطفاً یک محصول انتخاب کنید</option>
                          <?php foreach ($purchased_products as $product_id => $product_name): ?>
                            <option value="<?php echo esc_attr($product_id); ?>"><?php echo esc_html($product_name); ?></option>
                          <?php endforeach; ?>
                        </select>
                      <?php else: ?>
                        <div class="alert-notf-department">
                          <p>شما هنوز محصولی خریداری نکردید </p>
                        </div><?php endif; ?>
                    <?php endif; ?>

                    <?php if (tkm_settings('edd_setting')): ?>
                      <?php
                      $current_user = wp_get_current_user();
                      if (function_exists('edd_get_users_purchases')) {
                        $current_user = wp_get_current_user();
                        $payments = edd_get_users_purchases($current_user->ID, -1);
                      }                ?>
                      <?php if ($payments) : ?>
                        <label class="tkm_label">محصول دیجیتال</label>
                        <select class="tkm_input_form" name="edd_purchased_products" id="edd-products">
                          <option value="">لطفا یک محصول دیجیتال انتخاب کنید</option>
                          <?php
                          foreach ($payments as $payment) {
                            $cart_items = edd_get_payment_meta_cart_details($payment->ID);
                            if ($cart_items) {
                              foreach ($cart_items as $item) {
                                $product_id = $item['id'];
                                $title = get_the_title($product_id);
                                echo '<option value="' . esc_attr($product_id) . '">' . esc_html($title) . '</option>';
                              }
                            }
                          }
                          ?>
                        </select>
                      <?php else : ?>
                        <div class="alert-notf-department">
                          <p>شما هنوز محصول دیجیتالی خریداری نکرده‌اید</p>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>

                    <label class="tkm_label priority_label" for="importance"> <?php echo tkm_settings('priority_label') ? tkm_settings('priority_label') : 'اهمیت تیکت' ?></label>
                    <select class="tkm_input_form" name="importance" id="importance">
                      <option value="low">کم</option>
                      <option value="medium">متوسط</option>
                      <option value="high">زیاد</option>
                    </select>
                    <br>
                    <label class="tkm_label description_label" for="description" style="margin-top: 15px;"><?php echo tkm_settings('dec_label') ?  tkm_settings('dec_label') : 'توضیحات' ?></label>
                    <?php wp_editor($content, $editor_id, $settings); ?>

                    <div class="reply-attachment ">
                      <label for="file-upload" s class="attachment-file" style="margin: 0;"> <?php echo tkm_settings('file_label') ?  tkm_settings('file_label') : 'آپلود فایل ' ?>
                        <img width="18px" style="height: 18px;" src="<?php echo TKM_FRONT_ASSETS . '/images/upload.png' ?>" alt="">
                      </label>
                      <input id="file-upload" class=" file_upload_reply" type="file" />

                      <button class="attachment-file" style="border: 0px !important;" type="button" id="openPopup"> <?php echo tkm_settings('voice_label') ?  tkm_settings('voice_label') : 'ارسال صدا ' ?>
                        <img width="18px" style="height: 18px;" src="<?php echo TKM_FRONT_ASSETS . '/images/microphone.png' ?>" alt="">
                      </button>

                    </div>
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
                    <input type="hidden" id="audioData" name="audioData">
                    <button type="submit" class="submit-reply" style="margin-top: 30px;">
                      ارسال تیکت
                      <span class="loader-submit"> در حال ارسال</span>
                      <img width="18px" style="height: 18px;" src="<?php echo TKM_FRONT_ASSETS . '/images/send.png' ?>" alt="">
                    </button>

                  </div>

            </form>
          </div>
        </div>
      <?php else: ?>
        <div class="alert-notf-department">
          <p>دپارتمانی یافت نشد</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>