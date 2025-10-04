<?php
defined('ABSPATH') || exit ("no access");
if( empty($this->c10fa0c0a84d1b555979978f6d4bb9c) ): ?>
    <div class="notice notice-error">
        <?php if (version_compare(PHP_VERSION, '7.0.0') >= 0):?>
        <p>
            <?php printf(esc_html__( 'To activating %s, please insert your license key', 'guard-gn-f586260e7794457b90ad708b29b03d' ), esc_html__($this->d4bbb2c5264552c85838873ecbf, 'guard-gn-f586260e7794457b90ad708b29b03d')); ?>
            <a href="<?php echo admin_url( 'admin.php?page='.$this->f1180a3e5dc1edc79ff9b01f3af ); ?>" class="button button-primary"><?php _e('Active License', 'guard-gn-f586260e7794457b90ad708b29b03d'); ?></a>
        </p>
        <?php else:?>
            <p>
                <?php printf(esc_html__( 'The PHP version of the website is lower than 7.0. Ask your host administrator to upgrade PHP version to activate %s. ', 'guard-gn-f586260e7794457b90ad708b29b03d' ), esc_html__($this->d4bbb2c5264552c85838873ecbf, 'guard-gn-f586260e7794457b90ad708b29b03d')); ?>
            </p>
    <?php endif; ?>
    </div>
<?php elseif( $this->aabf2e6070a538cc7d64f4e8b4d89e===true ): ?>
    <div class="notice notice-error">
        <p>
            <?php printf(esc_html__( 'Something is wrong with your %s license. Please check it.', 'guard-gn-f586260e7794457b90ad708b29b03d' ), esc_html__($this->d4bbb2c5264552c85838873ecbf, 'guard-gn-f586260e7794457b90ad708b29b03d')); ?>
            <a href="<?php echo admin_url( 'admin.php?page='.$this->f1180a3e5dc1edc79ff9b01f3af ); ?>" class="button button-primary"><?php _e('Check Now', 'guard-gn-f586260e7794457b90ad708b29b03d'); ?></a>
        </p>
    </div>
<?php endif; ?>