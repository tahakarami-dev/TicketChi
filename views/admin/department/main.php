

<div class="main-container">
<?php if ($_GET['page'] == 'tkm-departments') : ?>
    <div class="right_container">
        <span class="head-title">دپارتمان جدید</span>
        <form class="department-form" method="post">
            <?php wp_nonce_field('add_department', 'add_department_nonce', false) ?>


            <label for="">عنوان دپارتمان <span style="color:#d63638">*</span></label>
            <input class="department_input" name="name" type="text">
            <label for="">واحد دپارتمان <span style="color:#d63638">*</span></label>
            <select class="department_input" name="parent" id="">
                <option value="">بدون والد</option>
                <?php if (count($departments)): ?>
                    <?php foreach ($departments as $department): ?>

                        <?php if ($department->parent) {

                            continue;
                        } ?>

                        <option value="<?php echo esc_attr($department->ID) ?>"><?php echo esc_html($department->name) ?></option>

                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <label for="">کارمند دپارتمان <span style="color:#d63638">*</span></label>
            <select class="department_input" name="department-answerabel[]" id="department-answerabel" multiple>
                <option value=""></option>
            </select>


            <label for="">موقعیت دپارتمان <span style="color:#d63638">*</span></label>
            <input class="department_input" name="position" type="text">
            <label for="">توضیح کوتاه دپارتمان <span style="color:#d63638">*</span></label>
            <textarea class="department_input" name="description" id=""></textarea>
            <button class="btn-submit-department" name="submit" id="submit" type="submit">افزودن دپارتمان</button>



        </form>
    </div>
    <div class="left_container">
        <span class="head-title">دپارتمان ها</span>
        <table class="wp-list-table widefat fixed striped table-view-list pages department_table" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" id="title" class="manage-column column-title">عنوان</th>
                    <th scope="col" id="slug" class="manage-column column-slug">واحد</th>
                    <th scope="col" id="users" class="manage-column column-users">کارمند پاسخگو</th>
                    <th scope="col" id="status" class="manage-column column-status">موقیعت</th>
                </tr>
            </thead>
            <tbody id="the-list">

                <?php if (count($departments)): ?>
                    <?php foreach ($departments as $department): ?>
                        <tr>
                            <td class="title column-title"><?php echo esc_html($department->name) ?>

                                <div class="row-actions">

                                    <span><a class="edit" href="<?php echo esc_url(admin_url('admin.php?page=tkm-departments&action=edit&id=' . $department->ID)) ?>"> ویرایش </a></span>
                                    <span><a style="color: red;" class="delete" href="<?php echo wp_nonce_url(admin_url('admin.php?page=tkm-departments&action=delete&id=' . $department->ID), 'delete_department', 'delete_department_nonce') ?>">حذف</a></span>

                                </div>

                            </td>
                            <td class="slug column-slug">
                                <?php if ($department->parent) {

                                    $parent =   $this->get_a_department($department->parent);
                                    echo $parent ? $parent->name : '__';
                                } else {
                                    echo '__';
                                }
                                ?>
                            </td>
                            <?php
                            $answer = new TKM_Answerable_Mnager();
                            $user_answer_id =   $answer->get_by_department($department->ID);


                            ?>
                            <?php foreach ($user_answer_id as $id): ?>
                                <?php $user =  get_userdata($id) ?>
                                <td class="users column-users"><?php echo $user->display_name ?> </td>
                            <?php endforeach; ?>
                            <td class="status column-status"><?php echo esc_html($department->position) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
    <?php else :  ?>
        <?Php $user = get_current_user_id(  );
$tickets = tkm_get_user_tickets_by_department($user);
        
            ?> 

        
        <div class="left_container">
        <span class="head-title"> تیکت های دپارتمان من</span>
        <table class="wp-list-table widefat fixed striped table-view-list pages department_table" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" id="title" class="manage-column column-title">عنوان</th>
                    <th scope="col" id="slug" class="manage-column column-depapartment">دپارتمان</th>
                    <th scope="col" id="users" class="manage-column column-creator"> ایجاد کننده</th>
                    <th scope="col" id="status" class="manage-column column-status">وضعیت</th>
                    <th scope="col" id="status" class="manage-column column-priority">اهمیت</th>
                    <th scope="col" id="status" class="manage-column column-create-date">تاریخ ایجاد </th>
                    <th scope="col" id="status" class="manage-column column-reply-date">تاریخ آخرین  پاسخ </th>


                </tr>
            </thead>
            <tbody id="the-list">

                <?php if (count($tickets)): ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td class="title column-title"><?php echo esc_html($ticket['title']) ?>

                                <div class="row-actions">

                                    <span><a class="edit"  href="<?php echo esc_url(admin_url('admin.php?page=tkm-edit-ticket&id=' . $ticket['ID'])) ?>"> ویرایش </a></span>

                                </div>

                            </td>
                            <td class="column-depapartment">
                           <span><?php echo get_department_html($ticket['department_id']) ?></span>
                            </td>
                        
                                <td class="users column-creator"><?php echo get_userdata($ticket['creator_id'] )->display_name?></td>
                            <td class="status column-status"><span><?php echo get_status_name($ticket['status']) ?></span></td>
                            <td class="priority column-priority"><span><?php echo get_priority_name($ticket['priority']) ?></span></td>
                            <td class="create_date column-create_date"><span><?php echo jdate($ticket['create_date']) ?></span></td>
                            <td class="reply_date column-reply_date"><span><?php echo jdate($ticket['reply_date']) ?></span></td>


                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
        <?php endif; ?>
<?php 

