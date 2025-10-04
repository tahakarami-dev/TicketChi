<?php
$user_id = get_current_user_id();

$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'reply-date';
$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
$priority = isset($_GET['priority']) ? $_GET['priority'] : 'all-priority';


$ticket_manager = new TKM_Ticket_Manager();
$tickets = $ticket_manager->get_tickets($user_id, $type, $status, $orderby, $page_num, $priority,);
$total_count = $ticket_manager->ticket_count($user_id, $type, $status, $orderby);

$department_manager = new TKM_Front_Department_Manager();
$statuses = tkm_get_status();


?>
 

<div class="container_tkm mt-5">
    <div class="header_tkm flex items-center  justify-between">
        <div class="header-right">
            <h3 class="title-page-tickets" style="font-size: 32px; font-weight:bold;"><?php echo tkm_settings('title_dashboard') ? tkm_settings('title_dashboard') : 'داشبورد تیکت‌ها ' ?></h3>
            <p class="text-header"><?php echo tkm_settings('description_dashboard') ? tkm_settings('description_dashboard') : 'مشاهده، ویرایش و مدیریت تیکت‌های دریافت شده و ارسال شده' ?></p>
        </div>
        <div class="header-left">
            <a href="<?php echo TKM_Ticket_Url::new(); ?>" style="background-color: #2A48CD; color:white;text-decoration: none;" class="flex new-ticket-btn w-52 h-16 justify-center items-center rounded-3xl  border-0"><svg style="margin-left: 5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                باز کردن تیکت جدید
            </a>
        </div>
    </div>

    <div class="flex passage_status flex-col">
        <!-- new code -->
        <div class="box-status-1">
            <div class="box_icon" style="color:#2A48CD;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
            </div>
            <h5 class="name-status">تیکت ها</h5>
            <span class="number-status"><?php echo convert_to_persian_numbers($ticket_manager->ticket_count($user_id, NULL, NULL)) ?></span>
        </div>

        <!--  -->
        <?php foreach ($statuses as $item): ?>
            <div class="box-status">
                <div class="box_icon" style="color:<?php echo $item['color'] ?> ">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>

                </div>
                <h5 class="name-status"><?php echo $item['name'] ?></h5>
                <span class="number-status"> <?php echo convert_to_persian_numbers($ticket_manager->ticket_count($user_id, NULL, $item['slug'])) ?></span>
            </div>
        <?php endforeach ?>

    </div>
</div>
<div class="main_tickets rounded-3xl mt-5" style="background-color: rgba(242, 242, 242, 1);">
    <div class="header_main">
        <form class="flex form-filter" method="get">
            <select class="filter" name="type" style="color: black ;">
                <option value="all" <?php selected($type, 'all') ?>>نوع تیکت</option>
                <option value="send" <?php selected($type, 'send') ?>>ارسالی</option>
                <option value="get" <?php selected($type, 'get') ?>>دریافتی</option>
            </select>
            <select class="filter" name="status">
                <option value="all" <?php selected($status, 'all') ?>>وضعیت تیکت</option>
                <?php foreach ($statuses as $_status): ?>
                    <option <?php selected($status, $_status['slug']) ?> value="<?php echo $_status['slug'] ?>"><?php echo $_status['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <select class="filter" name="orderby">
                <option <?php selected($orderby, 'reply-date') ?> value="reply-date">تاریخ پاسخ</option>
                <option <?php selected($orderby, 'create-date') ?> value="create-date">تاریخ ایجاد</option>
            </select>

            <select class="filter" name="priority">
                <option <?php selected($priority, 'all-priority') ?> value="all-priority"> اولویت</option>
                <option <?php selected($priority, 'low') ?> value="low">کم </option>
                <option <?php selected($priority, 'medium') ?> value="medium">متوسط </option>
                <option <?php selected($priority, 'high') ?> value="high">زیاد </option>
            </select>
            <button type="submit" id="tkm-filter-btn" class="tkm-filter-btn">فیلتر</button>
        </form>
    </div>

    <div class="list_tickets mt-4">
        <?php if ($tickets) : ?>
            <?php foreach ($tickets as $ticket): ?>
                <div class="ticket items-center justify-between pt-3" style="margin: 17px;">
                    <div class="tk_right">
                        <span class="font-bold title-ticket">
                            <a style="text-decoration: none;" class="link-title" href="<?php echo TKM_Ticket_Url::single($ticket->ID) ?>">
                                <?php echo esc_html($ticket->title) ?>
                            </a>
                        </span>
                        <?php $department = $department_manager->get_department($ticket->department_id) ?>
                        <p class="text-right department-name"><?php echo esc_html($department->name) ?></p>
                    </div>

                    <div class="right-2 flex">
                        <?php $user_data = get_userdata($ticket->creator_id);
                        $default_avatar = TKM_FRONT_ASSETS . 'images/user-profile-icon-in-flat-style-member-avatar-illustration-on-isolated-background-human-permission-sign-business-concept-vector.jpg';
                        $url_avatar = get_avatar_url($ticket->creator_id, ['default' => $default_avatar]); ?>
                        <img class="user_avatar" onerror="this.onerror=null; this.src='<?php echo $default_avatar ?>';" src="<?php echo $url_avatar ?>" alt="">
                        <p class="username"><?php echo $user_data->display_name ?></p>
                    </div>

                    <div class="tk_center">
                        <div>
                            <p class="create_date"><?php echo jdate($ticket->create_date, "Y-m-d H:i") ?></p>
                        </div>
                        <div class="status_circle_box">
                            <div class="main_circle">
                                <div class="circle" style="background-color: <?php echo get_status_color($ticket->status) ?>"></div>
                            </div>
                            <p class="status_name"><?php echo get_status_name($ticket->status) ?></p>
                        </div>
                    </div>

                    <div id="btn-show" class="tk_left flex justify-center items-center mb-4">
                        <a class="flex p-4 btn-eye" style="text-decoration: none;" href="<?php echo TKM_Ticket_Url::single($ticket->ID) ?>">
                            <svg style="margin-left: 7px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span class="btn-ticket-show">مشاهده</span>
                        </a>
                    </div>
                </div>

                <!-- Mobile Code -->
                <div class="ticket_mobile">
                    <div class="right_mobile">
                        <span class="font-bold title_ticket_mobile right_text">
                            <a style="text-decoration: none;" class="link_title_mobile" href="<?php echo TKM_Ticket_Url::single($ticket->ID) ?>">
                                <?php echo esc_html($ticket->title) ?>
                            </a>
                        </span>
                        <p class="text-right department_name_mobile right_text"><?php echo esc_html($department->name) ?></p>
                    </div>

                    <div class="center_mobile">
                        <p class="username_mobile"><?php echo $user_data->display_name ?></p>
                    </div>

                    <div class="left_mobile">
                        <p class="create_date_mobile"><?php echo jdate($ticket->create_date, "Y-m-d H:i") ?></p>
                    </div>

                    <div class="left_2_mobile">
                        <div class="main_circle_mobile">
                            <div class="circle_mobile" style="background-color: <?php echo get_status_color($ticket->status) ?>"></div>
                        </div>
                        <div>
                            <p class="status_name_mobile"><?php echo get_status_name($ticket->status) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php
            $per_page = 10;
            $big = 99999999999999;

            $args = [
                'base' => preg_replace('/\?.*/', '', get_pagenum_link()) . '%_%',
                'format' => '?page_num=%#%',
                'current' => max(1, $page_num),
                'total' => ceil($total_count / $per_page),
                'type' => 'list',
                'prev_next' => false

            ];
            ?>
            <div class="link_page">
                <?php echo paginate_links($args) ?>
            </div>
            <?php
            ?>
        <?php else: ?>
            <div class="no-tickets">
                <p>هیچ تیکتی یافت نشد.</p>
            </div>
        <?php endif; ?>

    </div>

</div>
</div>