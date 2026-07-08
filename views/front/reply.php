    <div class="divider">
        <hr>
        <span> پاسخ ها</span>
        <hr>
    </div>
    <?php if (tkm_settings('auto_reply')): ?>
        <div class="response-item-chat bot">
            <p class="body_ticket_text"><?php echo tkm_settings('auto_reply_text') ?></p>
            <p class="author-name" style="font-size: 14px !important;">ربات پاسخگو</p>

        </div>
    <?php endif; ?>
    <?php foreach ($replies as $reply): ?>


        <div class="response-item-chat">
            <?php $user_data = get_userdata($reply->creator_id) ?>
            <p class="timestamp"><?php ?></p>
            <p class="text_body_reply"><?php echo strip_tags($reply->body) ?></p>
            <?php if ($reply->file): ?>
                <div class="file_reply">
                    <a class="link_file_ticket" href="<?php echo $reply->file ?>" download="<?php echo $reply->file ?>">فایل پیوست: <?php echo tkm_get_file_name($reply->file)  ?></a>
                </div>
            <?php endif; ?>

            <?php if ($reply->voice): ?>
                <div class="voice_reply">
                    <audio class="voice_ticket" controls>
                        <source src="<?php echo $reply->voice  ?>" type="audio/wav">
                    </audio>
                </div>
            <?php endif; ?>
            <div class="reply-info" style="margin-top: 10px;">
                <p style="font-size: 14px;" class="tkm_user_name"><strong>

                        <?php
                        $default_avatar = TKM_FRONT_ASSETS . 'images/user-profile-icon-in-flat-style-member-avatar-illustration-on-isolated-background-human-permission-sign-business-concept-vector.jpg';
                        $url_avatar = get_avatar_url($reply->creator_id, ['default' => $default_avatar]); ?>
                        <img class="user_avatar_reply" onerror="this.onerror=null; this.src='<?php echo $default_avatar ?>';" src="<?php echo $url_avatar ?>" alt="">


                    </strong><?php echo $user_data->display_name ?></p>
                <p style="font-size: 14px;" class="timestamp reply_time"><?php echo wp_date( get_option('date_format') . ' ' . get_option('time_format'), strtotime($ticket->create_date) ); ?></p>

            </div>
        </div>

    <?php endforeach; ?>