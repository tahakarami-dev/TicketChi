<?php

$page = $_REQUEST['page'] ?? NULL;
$department_id = isset($_REQUEST['department_id']) ? $_REQUEST['department_id'] : null;
$priority = isset($_REQUEST['priority']) ? $_REQUEST['priority'] : null;
$creator_id =  isset($_REQUEST['creator_id']) ? $_REQUEST['creator_id'] : null;
$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : null;




$department_manager = new TKM_Admin_Department_Manager();
$paernt_departmnet = $department_manager->get_parent_department();

$statuses = tkm_get_status();


?>


<div class="wrap ticket-list mb-4">
    <header class="head-page">
        <h1 class="title_page_tickets">تیکت ها </h1>

        <a href="?page=tkm-new-ticket" class="page-title-action btn_add_new_tickets" style="margin-right: 5px;">ارسال تیکت جدید</a>
        <?php if ($search): ?>
            <span class="search_result" style="margin-right: 10px; font-size:16px;">نتایج جستجو <strong><?php echo $search ?></strong></span>
        <?php endif; ?>
    </header>

    <ul style="display: flex">
        <?php
        $ticket_manager = new TKM_Ticket_Manager();
        $tickets_counts = $ticket_manager->get_count_tickets();


        ?>
        <li style="margin-left:10px; "><a class="all_tickets" href="admin.php?page=tkm-tickets"> همه </a><strong class="num-tickets"> (<?php echo  $tickets_counts ?>)</strong></li>
        <?php foreach ($statuses as $status): ?>
            <li class="" style="margin-left:10px;">
                <a class="filter_link" style=" color:<?php echo $status['color'] ?> ;    text-decoration: none;
" href="admin.php?page=tkm-tickets&status=<?php echo $status['slug'] ?>"><?php echo   esc_html($status['name'])  ?></a>
            </li>
        <?php endforeach; ?>

    </ul>

    <div class="">
        <form class="container_filter" method="get">
            <input type="hidden" name="page" value="<?php echo $page ?>">
            <select id="filter1 filter_ticket main_filter" name="department_id">
                <option value=""> تمام دپارتمان ها </option>
                <?php foreach ($paernt_departmnet as $parent): ?>
                    <?php if (count($paernt_departmnet)): ?>
                        <optgroup label="<?php echo esc_attr($parent->name) ?>">
                            <?php $child_department = $department_manager->get_child_department($parent->ID) ?>
                            <?php if (count($child_department)): ?>
                                <?php foreach ($child_department as $child): ?>
                                    <option <?php selected($department_id, $child->ID) ?> value="<?php echo $child->ID ?>"><?php echo esc_html__($child->name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </optgroup>

                    <?php endif; ?>
                <?php endforeach; ?>

            </select>

            <select id="filter2 filter_ticket main_filter" name="priority">
                <option value="">تمام اولویت ها</option>
                <option <?php selected($priority, 'low') ?> value="low">کم</option>
                <option <?php selected($priority, 'medium') ?> value="medium"> متوسط</option>
                <option <?php selected($priority, 'high') ?> value="high"> زیاد </option>

            </select>

            <select class="user_search" id="ticket-creator" name="creator_id">
            <option value="" selected>  کاربر ایجاد کننده </option>

                <?php if ($creator_id) {
                    $user_data = get_userdata($creator_id);
                    echo '<option value="' . esc_attr($creator_id) . '" selected>' . $user_data->user_login .  '</option>';
                }
                ?>
            </select>
            <div class="filter_box" style="margin-top: 7px;">
                <input type="search" value="<?php echo $search ?>" name="search" placeholder="جستجو..." class="wp-filter-search filter_ticket filter_search">

                <input type="submit" value="فیلتر" class="filter_button" style="  background-color: #2947cc !important;
    color: white !important;
    border: none !important;
    padding: 6px 10px !important; border-radius:3px  !important;">
            </div>
        </form>
    </div>
    <form action="" method="post">
        <?php
        $this->tickets_list->prepare_items();
        $this->tickets_list->display();
        ?>
    </form>
</div>