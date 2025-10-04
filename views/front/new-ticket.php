<?php
$department_manage = new TKM_Front_Department_Manager();
$parent_departments =  $department_manage->get_parent_department();
$ticket_manager = new TKM_Ticket_Manager;

$content = ''; // محتوای پیش‌فرض
$editor_id = 'ticket-content'; // شناسه یکتا
$settings = array(
  'textarea_name' => 'my_custom_content', // نام برای ذخیره
  'media_buttons' => false, // دکمه‌های آپلود غیرفعال
  'editor_height' => 300, // ارتفاع ادیتور
  'tinymce' => array(
    'toolbar1' => 'bold,italic,underline,|,link,unlink,|,undo,redo', // تنها دکمه‌های انتخابی
    'block_formats' => 'Paragraph=p; Heading 2=h2; Heading 3=h3',
    'valid_elements' => '*[*]', // اجازه به تمام تگ‌ها
    'plugins' => 'link', // تنها پلاگین‌های انتخابی (لینک فعال است)
  ),
  'quicktags' => true, // ابزارک‌های HTML فعال
);


?>


<div class="container-ticket">

  <div class="header_1">
    <a class="btn_show_tickets" href="<?php echo TKM_Ticket_Url::all(); ?>">همه تیکت ها

    </a>
    <h5 style="margin-bottom: 0px; color:black !important; text-align:left;">ایجاد تیکت جدید</h5>
  </div>

  <?php if (tkm_settings('new-ticket-alert')) : ?>
    <div class="alert-info">
      <p class="alert_message"><?php echo tkm_settings('alert-message-ticket'); ?></p>
    </div>
  <?php endif; ?>

  <?php if (tkm_settings('faq_swicher') == 1): ?>
    <div class="faq_sec">

      <h4 class="faqs">سوالات متداول</h4>

      <?php foreach (tkm_settings('faqs') as $faq): ?>
        <div class="accordion">
          <p class="title_faq"><?php echo esc_html($faq['faqs-title']); ?></p> <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
        </div>
        <div class="accordion-content">
          <p class="body_faq"><?php echo trim($faq['faqs-body']); ?></p>
        </div>

      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="sec-not-faqs">
    <a class="btn-not-faqs">درخواست ثبت تیکت </a>
  </div>

  <div class="main_content_form" style="display: none;">
    <div class="form_container">
      <form id="tkm-submit-ticket" action="" method="POST">
        <?php if (isset($parent_departments) && count($parent_departments) > 0): ?>
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
                      <option value="">لطفاً یک محصول انتخاب کنید</option> <!-- گزینه پیش‌فرض -->
                      <?php foreach ($purchased_products as $product_id => $product_name): ?>
                        <option value="<?php echo esc_attr($product_id); ?>"><?php echo esc_html($product_name); ?></option>
                      <?php endforeach; ?>
                    </select>
                  <?php else: ?>
                    <div class="alert-notf-department">
                      <p>شما هنوز محصولی خریداری نکردید </p>
                    </div><?php endif; ?>
                <?php endif; ?>

                <label class="tkm_label priority_label" for="importance"> <?php echo tkm_settings('priority_label') ? tkm_settings('priority_label') : 'اهمیت تیکت' ?></label>
                <select class="tkm_input_form" name="importance" id="importance">
                  <option value="low">کم</option>
                  <option value="medium">متوسط</option>
                  <option value="high">زیاد</option>
                </select>
                <br>


                <!-- <label  class="tkm_label" for="name">نام</label>
      <input  class="tkm_input_form"  type="text" id="name">

      <label  class="tkm_label" for="email">ایمیل</label>
      <input  class="tkm_input_form"  type="email" id="email">

      <label  class="tkm_label" for="phone">شماره موبایل</label>
      <input  type="text" id="phone"> -->

                <label class="tkm_label description_label" for="description" style="margin-top: 15px;"><?php echo tkm_settings('dec_label') ?  tkm_settings('dec_label') : 'اتوضیحات' ?></label>
                <?php wp_editor($content, $editor_id, $settings);?>

                <div class="file_box">
                  <label for="file-upload" class="custom-file-upload km_label "> <?php echo tkm_settings('file_label') ?  tkm_settings('file_label') : 'آپلود فایل ' ?><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icon_upload">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" />
                    </svg>

                  </label>
                  <input id="file-upload" class=" file_upload_reply" type="file" />
                  
                  <button class="custom-file-upload voice_btn" style="border: 0px !important;" type="button" id="openPopup"> <?php echo tkm_settings('voice_label') ?  tkm_settings('voice_label') : 'ارسال صدا ' ?>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icon_upload">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z" />
                    </svg>
                  </button>

                </div>
                <div id="popup">
                  <h2 class="title_voice" style="margin-bottom: 10px;">ضبط پیام صوتی</h2>
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

                <input type="hidden" id="audioData" name="audioData">
                <br><br>

                <button type="submit" class="tkm-submit-ticket">
                  ارسال تیکت
                  <span class="loader-submit"> در حال ارسال</span>

                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 icon_send">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                  </svg>

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