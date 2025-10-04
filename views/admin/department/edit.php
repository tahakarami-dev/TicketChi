<div class="main-edit-container">
<span class="head-title">ویرایش دپارتمان </span>
<form class="department-form " method="post">
    <?php wp_nonce_field('add_department', 'add_department_nonce', false); ?>
    <?php if (isset($showdepartment->ID)): ?>
        <input type="hidden" name="department_id" value="<?php echo esc_attr($showdepartment->ID); ?>">
    <?php endif; ?>


            <label for="">عنوان دپارتمان</label>
            <input value="<?php echo esc_attr($showdepartment->name ?? ''); ?>" name="name" type="text">
            <label for="">واحد دپارتمان</label>
            <select name="parent" id="">
                <option value="">بدون والد</option>
                <?php foreach ($departments as $department): ?>
                        <?php if ($department->parent || ($showdepartment->ID ?? 0) == $department->ID) continue; ?>
                        <option value="<?php echo esc_attr($department->ID); ?>" <?php selected($showdepartment->parent ?? 0, $department->ID); ?>>
                            <?php echo esc_html($department->name); ?>
                        </option>
                    <?php endforeach; ?>
            </select>
            <!-- <label for="">کارمند دپارتمان (قابل تغییر نیست)</label>
            <select disabled style="pointer-events: none;" name="department-answerabel[]" id="department-answerabel" multiple>
                <option value=""></option>
                     <?php
                    if (count($answerable)) {
                        foreach ($answerable as $user_id) {
                            $user_data = get_userdata($user_id);
                            echo '<option value="'.$user_id.'" selected>'.$user_data->user_login.'</option>';
                        }
                    }
                    ?>
            </select> -->
    
            <label for="">موقعیت دپارتمان</label>
            <input  value="<?php echo esc_attr($showdepartment->position ?? ''); ?>" name="position" type="text">
            <label for="">توضیح کوتاه دپارتمان</label>
            <textarea name="description" id=""><?php echo esc_textarea($showdepartment->description ?? ''); ?></textarea>
            <div class="btn-sec">
            <button class="btn-submit-department" name="submit" id="submit" type="submit">ویرایش دپارتمان</button>

            </div>



        </form>
</div>