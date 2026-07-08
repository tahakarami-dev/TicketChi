<?php

$user_id = get_current_user_id();

$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'reply-date';
$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
$priority = isset($_GET['priority']) ? $_GET['priority'] : 'all-priority';

$ticket_manager = new TKM_Ticket_Manager();
$tickets = $ticket_manager->get_tickets($user_id, $type, $status, $orderby, $page_num, $priority);

$total_count = $ticket_manager->ticket_count($user_id, $type, $status, $orderby);

$department_manager = new TKM_Front_Department_Manager();

$statuses = tkm_get_status();

$is_shortcode = tkm_settings('shortcode');
?>
<?php if (is_user_logged_in()): ?>
    <div class="tkm-shortcode-wrapper">
        <div class="container_tkm">
            <div class="header_tkm flex items-center  justify-between">
                <div class="header-right">
                    <h2 class="title-page-tickets"><?php echo tkm_settings('title_dashboard') ? tkm_settings('title_dashboard') : 'داشبورد تیکت‌ها ' ?></h2>
                    <p class="text-header"><?php echo tkm_settings('description_dashboard') ? tkm_settings('description_dashboard') : 'مدیریت تیکت‌های پشتیبانی شما' ?></p>
                </div>
                <div class="header-left">
                    <a href="<?php echo $is_shortcode ? TKM_Shortcode_Url::new() : TKM_Ticket_Url::new(); ?>" class="flex new-ticket-btn justify-center items-center">
                        <img width="18px" src="<?php echo TKM_FRONT_ASSETS . '/images/plus.png' ?>" alt="">
                        ثبت تیکت جدید
                    </a>
                </div>
            </div>

            <div class="flex passage_status flex-col">
                <div class="status-box all-ticket-box">
                    <div class="box_icon" style="color:#2A48CD;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </div>
                    <h5 class="name-status">تیکت ها</h5>
                    <span class="number-status"><?php echo convert_to_persian_numbers($ticket_manager->ticket_count($user_id, NULL, NULL)) ?></span>
                </div>

                <?php foreach ($statuses as $item): ?>
                    <div class="status-box">
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
        <div class="main_tickets ">
            <div class="header_main">
                <form class="flex form-filter" method="get">
                    <select class="filter" name="type">
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

            <div class="tickets-list">
                <?php if ($tickets) : ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="ticket items-center justify-between">
                            <div class="tk_right">
                                <a class="ticket-title" href="<?php echo $is_shortcode ? TKM_Shortcode_Url::single($ticket->ID) : TKM_Ticket_Url::single($ticket->ID) ?>">
                                    <?php echo esc_html($ticket->title) ?>
                                </a>
                                <?php $department = $department_manager->get_department($ticket->department_id) ?>
                                <p class="text-right department-name"><?php echo esc_html($department->name) ?></p>
                            </div>
                            <div class="tk_center">
                                <div class="tk-status-box-inner">
                                    <button style="background-color: <?php echo get_status_color($ticket->status) ?>" class="status_name"><?php echo get_status_name($ticket->status) ?></button>
                                </div>
                                <div>
                                    <p class="reply_date"><?php echo wp_date(
                                                                get_option('date_format') . ' ' . get_option('time_format'),
                                                                strtotime($ticket->reply_date)
                                                            ); ?></p>
                                </div>
                            </div>

                            <div id="btn-show" class="tk_left flex ">
                                <a class="flex p-4 btn-eye" style="text-decoration: none;" href="<?php echo $is_shortcode ? TKM_Shortcode_Url::single($ticket->ID) : TKM_Ticket_Url::single($ticket->ID) ?>">
                                    <img width="20px" src="<?php echo TKM_FRONT_ASSETS . '/images/eye.png' ?>" alt="">
                                    <span class="btn-ticket-show">مشاهده</span>
                                </a>
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
                    <div class="not-found">
                        <p>هیچ تیکتی یافت نشد.</p>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
    </div>
<?php endif; ?>