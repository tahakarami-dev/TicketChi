<?php

$statuses = tkm_get_status();
$department_manager = new TKM_Admin_Department_Manager();
$parent_departments = $department_manager->get_parent_department();
$ticket_manager = new TKM_Ticket_Manager();
$ticket_id = isset($_GET['id']) ?  $_GET['id'] : 0;

if (isset($_GET['page']) && $_GET['page'] === 'tkm-edit-ticket') {
    $product_id = isset($ticket->product) ? $ticket->product : null;
    $product_info = $product_id ? get_product_name_and_link_by_id($product_id) : null;

    if (tkm_settings('edd_setting') == true) {
        $edd_product_id = $ticket->edd_product ? $ticket->edd_product : NULL;
        $edd_product_info = get_the_title($edd_product_id);
    }
    $ticket = $ticket_manager->get_info_ticket($ticket_id);
}

?>
<?php
if (isset($_GET['page'])) {
    if ($_GET['page'] === 'tkm-edit-ticket') {
        if (isset($_GET['id']) && !empty($_GET['id'])) {

?>
            <div class="main-container-admin-ticket">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-ticket-left">
                        <div class="right-form">
                            <h2 class="right-title"> <?php echo $is_edit ? 'تنظیمات ویرایش' : 'تنظیمات ارسال' ?></h2>
                            <div class="sec_one_right">
                                <?php if (!$is_edit): ?>
                                    <div class="alert-send">
                                        <span class="alert_title">ارسال پیغام اطلاع رسانی</span>
                                        <input type="checkbox" id="send-copy" name="send-copy">
                                    </div>
                                <?php endif; ?>

                                <span class="department-radio" for="">دپارتمان <span style="color: #d63638; font-size:14px">*</span> </span>
                                <?php if (count($parent_departments)): ?>
                                    <?php foreach ($parent_departments as $parent_department): ?>
                                        <p class="father_department"><?php echo esc_html($parent_department->name) ?></p>

                                        <?php
                                        $child_department = $department_manager->get_child_department($parent_department->ID);
                                        if (count($child_department)): ?>
                                            <?php foreach ($child_department as $child): ?>
                                                <label>
                                                    <input class="radio-select-department" <?php $is_edit ? checked($ticket->department_id, $child->ID) : null ?> type="radio" name="department_id" value="<?php echo esc_attr($child->ID); ?>">
                                                    <?php echo esc_html($child->name) ?>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <div class="edit_date">
                                    <?php if ($is_edit): ?>
                                        <span class="date_title">تاریخ تیکت</span>
                                        <a class="edit-date" href="">ویرایش</a>
                                        <input class="date_input" name="date_ticket" type="text" value="<?php echo wp_date( get_option('date_format') . ' ' . get_option('time_format'), strtotime($ticket->create_date) ); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="sub-sec">

                                    <div class="rate_ticket" style="margin-top: 20px;">
                                        <?php if ($is_edit): ?>
                                            <span class="department-radio" for="">امتیاز تیکت</span>
                                            <?php $rate = $ticket_manager->get_ticket_rating($ticket_id) ?>
                                            <p><?php echo 'امتیاز کاربر به تیکت (' . $rate . ')' ?> </p>
                                        <?php endif; ?>

                                    </div>

                                    <div class="note_ticket" style="margin-top: 20px;">
                                        <span class="department-radio" style="margin-bottom: 15px !important;" for="">یادداشت تیکت</span>

                                        <textarea style="width: 100%;" name="note" id="note-ticket"><?php echo esc_html($ticket->note) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="left-form">
                            <h2 class="title-form"> <?php echo $is_edit ? 'ویرایش تیکت' : 'ارسال تیکت جدید' ?> </h2>
                            <div class="one-sec-form">

                                <label class="lable_form" for="ticket-title">عنوان تیکت <span style="color: #d63638; font-size:14px">*</span></label>
                                <input class="input_form" value="<?php echo $is_edit ? esc_attr($ticket->title)  : null ?>" type="text" id="ticket-title" name="ticket-title" placeholder="عنوان تیکت را وارد کنید">

                                <label class="lable_form" id="lable_dec" for="tkm-content">توضیحات <span style="color: #d63638; font-size:14px">*</span></label>
                                <?php
                                $body = $is_edit ? $ticket->body : null;
                                ?>
                                <?php wp_editor($body, 'tkm-content', ['editor_height' => 150]) ?>

                                <label class="lable_form" for="status">وضعیت</label>
                                <select class="input_form" name="status" id="status">
                                    <?php foreach ($statuses as $_status): ?>
                                        <option <?php $is_edit ? selected($ticket->status, $_status['slug']) : null ?> value="<?php echo esc_attr($_status['slug']) ?>"><?php echo esc_attr($_status['name']) ?></option>
                                    <?php endforeach ?>
                                </select>

                                <label class="lable_form" for="priority">اولویت</label>
                                <select class="input_form" name="priority" id="priority">
                                    <option value="low" <?php $is_edit ? selected($ticket->priority, 'low') : null ?>>کم</option>
                                    <option <?php $is_edit ? selected($ticket->priority, 'medium') : null ?> value="medium">متوسط</option>
                                    <option <?php $is_edit ? selected($ticket->priority, 'high') : null ?> value="high">زیاد</option>
                                </select>


                                <?php if (tkm_settings('product_setting') &&  !empty($ticket->product)): ?>
                                    <label class="lable_form" for="user">محصول<span style="color: #d63638; font-size:14px"></span></label>
                                    <select class="user_input" name="#" id="product">
                                        <option value="<?php echo  esc_html($product_info['name']) ?>"><?php echo  esc_html($product_info['name']) ?></option>
                                    </select>
                                    <a class="view_profile_link user_view" target="_blank" href="<?php echo  esc_url($product_info['link']) ?>">مشاهده محصول</a>
                                <?php endif; ?>

                                <?php if (tkm_settings('edd_setting')): ?>
                                    <label class="lable_form" for="user">محصول دیجیتال<span style="color: #d63638; font-size:14px"></span></label>
                                    <select class="user_input" name="#" id="edd_product">
                                        <option value="<?php echo esc_html($edd_product_info) ?>"><?php echo  esc_html($edd_product_info) ?></option>
                                    </select>
                                    <?php $edd_product_link = get_permalink($edd_product_id); ?>
                                    <a class="view_profile_link user_view" target="_blank" href="<?php echo esc_html($edd_product_link)  ?>">مشاهده محصول دیجیتال</a>
                                <?php endif; ?>

                                <?php if ($is_edit): ?>
                                    <?php if ($ticket->creator_id): ?>
                                        <?php $user_creator_data = get_userdata($ticket->creator_id) ?>
                                        <label class="lable_form" for="user"> ایجاد کننده <span style="color: #d63638; font-size:14px">*</span></label>
                                        <select class="user_input" name="creator-id[]" id="ticket-creator">
                                            <option value="<?php echo esc_attr($ticket->creator_id) ?>"><?php echo $user_creator_data->user_login ?></option>
                                        </select>
                                        <a class="view_profile_link user_view" target="_blank" href="<?php echo get_edit_user_link($ticket->creator_id) ?>">مشاهده پروفایل</a>
                                    <?php endif; ?>

                                <?php endif; ?>

                                <label class="lable_form" for="user">کاربر <span style="color: #d63638; font-size:14px">*</span></label>

                                <?php if ($is_edit):
                                    $user_data = get_userdata($ticket->user_id);
                                ?>

                                    <select class=" user_input user_label" name="user-id[]" id="ticket-user-id">
                                        <?php if ($ticket->user_id) :  ?>
                                            <option selected value="<?php echo esc_attr($ticket->user_id) ?>"><?php echo  $user_data->user_login ?></option>
                                        <?php else: ?>
                                            <option selected value=""> لطفا کارمند پاسخ دهنده را انتخاب نمایید </option>

                                        <?php endif; ?>
                                    </select>
                                    <a class="view_profile_link user_view" target="_blank" href="<?php echo get_edit_user_link($ticket->user_id) ?>">مشاهده پروفایل</a>

                                <?php else: ?>
                                    <div class="input-group">
                                        <select class="input_form" name="user-id[]" id="ticket-user-id" multiple>
                                        </select>
                                    </div>
                                <?php endif;  ?>

                                <label class="lable_form" for="tkm_ticket_files">فایل پیوست</label>
                                <input class="input_form tkm-file-input" id="tkm_ticket_files" name="tkm_ticket_files[]" type="file" <?php echo tkm_settings('upload_multiple') ? 'multiple' : '' ?> accept="<?php echo esc_attr(tkm_get_accept_attr()) ?>">
                                <p class="tkm-upload-hint"><?php echo esc_html(tkm_upload_hint_text()) ?></p>

                                <?php $ticket_files = tkm_get_files($ticket->file); ?>
                                <?php if (!empty($ticket_files)): ?>
                                    <div class="tkm-admin-files">
                                        <?php foreach ($ticket_files as $tkm_file): ?>
                                            <div class="tkm-admin-file-row">
                                                <a class="view_profile_link" target="_blank" download="<?php echo esc_attr($tkm_file) ?>" href="<?php echo esc_url($tkm_file) ?>"><?php echo esc_html(tkm_get_file_name($tkm_file)) ?></a>
                                                <label class="tkm-remove-file">
                                                    <input type="checkbox" name="remove_ticket_file[]" value="<?php echo esc_attr($tkm_file) ?>"> حذف
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($is_edit && $ticket->voice): ?>
                                    <div class="proprties">
                                        <audio class="audio_controls" controls>
                                            <source src="<?php echo esc_url($ticket->voice) ?>" type="audio/wav">
                                        </audio>
                                    </div>
                                <?php endif; ?>
                                    <?php wp_nonce_field('ticket_send', 'ticket_nonce') ?>

                                    <input type="submit" name="publish" id="publish" class="submit-btn" value=" <?php echo $is_edit ? 'ویرایش تیکت' : 'ارسال' ?> ">
                            </div>
                        </div>
                    </div>
            </div>
            <?php if ($is_edit) : ?>
                <div class="chat-actions">
                    <div class="reply_container show_reply">
                        <h1 class="reply_title">گفتگو ها</h1>

                        <div class="content_reply">

                            <?php if (isset($replies) && is_array($replies) && count($replies)) : ?>
                                <?php foreach ($replies as $reply): ?>
                                    <div class="tab_header">

                                        <?php $user_data_reply = get_userdata($reply->creator_id) ?>

                                        <p class="message_content"><?php echo $reply->body ?></p>
                                        <?php if ($reply->voice) : ?>
                                            <audio class="reply_voice" controls>
                                                <source src="<?php echo $reply->voice  ?>" type="audio/wav">
                                            </audio>
                                        <?php endif; ?>
                                        <div class="infobox_reply">
                                            <div class="date_reply">
                                                <p><?php echo wp_date( get_option('date_format') . ' ' . get_option('time_format'), strtotime($ticket->create_date) ); ?>
                                                    </svg>
                                                </p>
                                            </div>
                                            <div class="username_reply">
                                                <span><?php echo $user_data_reply->display_name ?>
                                                </span>
                                            </div>
                                        </div>
                                        <button class="edit_btn_reply">ویرایش</button>

                                        <div style="display: none;" class="box-editor">
                                            <?php wp_editor($reply->body, 'tkm-reply-body-' . $reply->ID, ['editor_height' => 150]) ?>
                                        </div>

                                        <?php $reply_files = tkm_get_files($reply->file); ?>
                                        <?php if (!empty($reply_files)): ?>
                                            <div class="tkm-admin-files">
                                                <?php foreach ($reply_files as $reply_file): ?>
                                                    <div class="tkm-admin-file-row">
                                                        <a class="view_link_file_reply" target="_blank" href="<?php echo esc_url($reply_file) ?>"><?php echo esc_html(tkm_get_file_name($reply_file)) ?></a>
                                                        <label class="tkm-remove-file">
                                                            <input type="checkbox" name="remove_reply_file_<?php echo esc_attr($reply->ID) ?>[]" value="<?php echo esc_attr($reply_file) ?>"> حذف
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <input class="input_form tkm-file-input edit_reply_file" type="file" name="tkm_reply_files_<?php echo esc_attr($reply->ID) ?>[]" <?php echo tkm_settings('upload_multiple') ? 'multiple' : '' ?> accept="<?php echo esc_attr(tkm_get_accept_attr()) ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="reply_container">
                        <h1 class="reply_title">ارسال پاسخ </h1>
                        <p style="color: #616060;">* کاربر گرامی برای حذف پاسخ مورد نظر کافیست متن پاسخ را خالی بگذارید سپس تیکت را آپدیت کنید *</p>
                        <span>پاسخ آماده</span>

                        <select class="save_message" name="save-message" id="save_select">
                            <option value="">پاسخ آماده را انتخاب کنید </option>

                            <?php foreach (tkm_settings('save_message') as $item): ?>
                                <option value="<?php echo $item['message_save'] ?>"><?php echo  $item['save_message_title'] ?></option>
                            <?php endforeach; ?>

                        </select>

                        <label class="wpeditor_textarea" for="edit-text">ویرایشگر متن</label>
                        <?php wp_editor(null, 'reply_content', ['editor_height' => 150]) ?>

                        <label class="lable_form" for="tkm_reply_files">فایل پیوست پاسخ</label>
                        <input style="margin-bottom: 10px;" class="input_form tkm-file-input attechment_ticket" id="tkm_reply_files" name="tkm_reply_files[]" type="file" <?php echo tkm_settings('upload_multiple') ? 'multiple' : '' ?> accept="<?php echo esc_attr(tkm_get_accept_attr()) ?>">
                        <p class="tkm-upload-hint"><?php echo esc_html(tkm_upload_hint_text()) ?></p>
                        <input type="submit" name="publish" id="publish reply-send" class="submit-btn" value=" <?php echo $is_edit ? ' ارسال پاسخ ' : 'ارسال' ?> ">
                    </div>
                </div>
            <?php endif; ?>

            </div>

        <?php

        } else {
        ?>

        <?Php

        }
    } elseif ($_GET['page'] === 'tkm-new-ticket') { ?>
        <div class="main-container-admin-ticket">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-ticket-left">
                    <div class="right-form">
                        <h2 class="right-title"> <?php echo $is_edit ? 'تنظیمات ویرایش' : 'تنظیمات ارسال' ?></h2>
                        <div class="sec_one_right">
                            <div class="alert-send">
                                <span class="alert_title"> ارسال پیغام اطلاع رسانی </span>
                                <input type="checkbox" id="send-copy" name="send-copy">
                            </div>
                            <span class="department-radio" for="">دپارتمان <span style="color: #d63638; font-size:14px">*</span></span>
                            <?php if (count($parent_departments)): ?>
                                <?php foreach ($parent_departments as $parent_department): ?>
                                    <p class="father_department"><?php echo esc_html($parent_department->name) ?></p>
                                    <?php
                                    $child_department = $department_manager->get_child_department($parent_department->ID);
                                    if (count($child_department)): ?>
                                        <?php foreach ($child_department as $child): ?>
                                            <label>
                                                <input class="radio-select-department" <?php $is_edit ? checked($ticket->department_id, $child->ID) : null ?> type="radio" name="department_id" value="<?php echo esc_attr($child->ID); ?>">
                                                <?php echo esc_html($child->name) ?>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <div class="edit_date">
                                <?php if ($is_edit): ?>
                                    <span class="date_title">تاریخ تیکت</span>
                                    <a class="edit-date" href="">ویرایش</a>
                                    <input class="date_input" name="date_ticket" type="text" value="<?php echo date('Y-m-d H:i', strtotime($ticket->create_date))  ?>">
                                <?php endif; ?>
                            </div>

                            <div class="note_ticket" style="margin-top: 20px;">
                                <span class="department-radio" for="">یادداشت تیکت</span>
                                <textarea style="width: 100%;" name="note" id="note-ticket" placeholder=""></textarea>
                            </div>
                            <div class="sub-sec">
                            </div>
                        </div>
                    </div>
                    <div class="left-form">
                        <h2 class="title-form"> <?php echo $is_edit ? 'ویرایش تیکت' : 'ارسال تیکت جدید' ?> </h2>
                        <div class="one-sec-form">

                            <label class="lable_form" for="ticket-title">عنوان تیکت <span style="color: #d63638; font-size:14px">*</span></label>
                            <input class="input_form" value="<?php echo $is_edit ? esc_attr($ticket->title)  : null ?>" type="text" id="ticket-title" name="ticket-title" placeholder="عنوان تیکت را وارد کنید">

                            <label class="lable_form" for="tkm-content">توضیحات <span style="color: #d63638; font-size:14px">*</span></label>
                            <?php
                            $body = $is_edit ? $ticket->body : null;
                            ?>
                            <?php wp_editor($body, 'tkm-content', ['editor_height' => 150]) ?>

                            <label class="lable_form" for="status">وضعیت</label>
                            <select class="input_form" name="status" id="status">
                                <?php foreach ($statuses as $_status): ?>
                                    <option <?php $is_edit ? selected($ticket->status, $_status['slug']) : null ?> value="<?php echo esc_attr($_status['slug']) ?>"><?php echo esc_attr($_status['name']) ?></option>
                                <?php endforeach ?>
                            </select>

                            <label class="lable_form" for="priority">اولویت</label>
                            <select class="input_form" name="priority" id="priority">
                                <option value="low" <?php $is_edit ? selected($ticket->priority, 'low') : null ?>>کم</option>
                                <option <?php $is_edit ? selected($ticket->priority, 'medium') : null ?> value="medium">متوسط</option>
                                <option <?php $is_edit ? selected($ticket->priority, 'high') : null ?> value="high">زیاد</option>
                            </select>

                            <?php if ($is_edit): ?>
                                <?php if ($ticket->creator_id): ?>
                                    <?php $user_creator_data = get_userdata($ticket->creator_id) ?>
                                    <label class="lable_form" for="user">ایجاد کننده</label>
                                    <select class="input_form" name="creator-id[]" id="ticket-creator">
                                        <option value="<?php echo esc_attr($ticket->creator_id) ?>"><?php echo $user_creator_data->user_login ?></option>
                                    </select>
                                    <a class="view_profile_link user_view" target="_blank" href="<?php echo get_edit_user_link($ticket->creator_id) ?>">مشاهده پروفایل</a>
                                <?php endif; ?>

                            <?php endif; ?>
                            <label class="lable_form " for="user">کاربر <span style="color: #d63638; font-size:14px">*</span></label>

                            <?php if ($is_edit):
                                $user_data = get_userdata($ticket->user_id);
                            ?>
                                <select class="input_form user_label" name="user-id[]" id="ticket-user-id">
                                    <option selected value="<?php echo esc_attr($ticket->user_id) ?>"><?php echo  $user_data->user_login ?></option>
                                </select>
                                <a class="view_profile_link user_view" target="_blank" href="<?php echo get_edit_user_link($ticket->user_id) ?>">مشاهده پروفایل</a>

                            <?php else: ?>
                                <div class="input-group">
                                    <select class="input_form" name="user-id[]" id="ticket-user-id" multiple>
                                    </select>
                                </div>
                            <?php endif;  ?>

                            <label class="lable_form" for="tkm_ticket_files">فایل پیوست</label>
                            <input class="input_form tkm-file-input" id="tkm_ticket_files" name="tkm_ticket_files[]" type="file" <?php echo tkm_settings('upload_multiple') ? 'multiple' : '' ?> accept="<?php echo esc_attr(tkm_get_accept_attr()) ?>">
                            <p class="tkm-upload-hint"><?php echo esc_html(tkm_upload_hint_text()) ?></p>
                                <?php wp_nonce_field('ticket_send', 'ticket_nonce') ?>

                                <input type="submit" name="publish" id="publish" class="submit-btn" value=" <?php echo $is_edit ? 'ویرایش تیکت / ارسال پاسخ ' : 'ارسال' ?> ">
                        </div>
                    </div>
                </div>
        </div>
        </div>
<?php
    } else {
        echo "<p style='color: red;'>صفحه‌ای نامعتبر.</p>";
    }
} else {
    echo "<p style='color: red;'>پارامتر صفحه مشخص نشده است.</p>";
}
?>