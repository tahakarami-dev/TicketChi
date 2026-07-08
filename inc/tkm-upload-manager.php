<?php

defined('ABSPATH') || exit('NO Access');

class TKM_Upload_File
{

    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * حداکثر حجم مجاز هر فایل بر حسب بایت (بر اساس تنظیمات مدیریت آپلود).
     */
    public static function max_size_bytes()
    {
        $mb = (float) tkm_settings('upload_max_size');
        if ($mb <= 0) {
            $mb = 5;
        }
        return (int) round($mb * 1024 * 1024);
    }

    /**
     * لیست پسوندهای مجاز (بر اساس تنظیمات مدیریت آپلود).
     */
    public static function allowed_extensions()
    {
        $raw = tkm_settings('upload_allowed_ext');
        if (empty($raw)) {
            $raw = 'jpg,jpeg,png,gif,webp,pdf,zip,rar,doc,docx,xls,xlsx,txt,mp4,mp3';
        }

        $exts = array_map('trim', explode(',', strtolower($raw)));
        $exts = array_map(function ($ext) {
            return ltrim($ext, '.');
        }, $exts);

        return array_values(array_filter($exts));
    }

    /**
     * آیا امکان آپلود چند فایل فعال است؟
     */
    public static function is_multiple()
    {
        return (bool) tkm_settings('upload_multiple');
    }

    /**
     * حداکثر تعداد فایل مجاز در حالت چند فایلی.
     */
    public static function max_files()
    {
        $count = (int) tkm_settings('upload_max_files');
        return $count > 0 ? $count : 5;
    }

    /**
     * تبدیل ساختار $_FILES[key] به آرایه‌ای از فایل‌های مجزا.
     * هم حالت تکی و هم حالت آرایه‌ای (name[]) را پشتیبانی می‌کند.
     */
    public static function normalize($files)
    {
        $result = [];

        if (empty($files) || !isset($files['name'])) {
            return $result;
        }

        if (is_array($files['name'])) {
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if (!isset($files['error'][$i]) || $files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if (empty($files['name'][$i])) {
                    continue;
                }
                $result[] = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
            }
        } else {
            if (isset($files['error']) && $files['error'] !== UPLOAD_ERR_NO_FILE && !empty($files['name'])) {
                $result[] = $files;
            }
        }

        return $result;
    }

    /**
     * اعتبارسنجی یک فایل (پسوند و حجم). در صورت مجاز بودن true و در غیر این صورت پیام خطا برمی‌گرداند.
     */
    public function validate($file)
    {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = self::allowed_extensions();

        if (!empty($allowed) && !in_array($ext, $allowed, true)) {
            return 'پسوند فایل «' . $ext . '» مجاز نیست. پسوندهای مجاز: ' . implode('، ', $allowed);
        }

        if ($file['size'] > self::max_size_bytes()) {
            $mb = round(self::max_size_bytes() / (1024 * 1024), 1);
            return 'حجم فایل «' . $file['name'] . '» بیشتر از حد مجاز (' . $mb . ' مگابایت) است';
        }

        return true;
    }

    /**
     * آپلود یک یا چند فایل.
     * خروجی: ['success' => bool, 'urls' => array, 'url' => string|null, 'message' => string]
     */
    public function upload()
    {
        $files = self::normalize($this->file);

        // انتخاب نکردن فایل خطا محسوب نمی‌شود (فایل اختیاری است)
        if (empty($files)) {
            return ['success' => true, 'urls' => [], 'url' => null, 'message' => ''];
        }

        if (self::is_multiple()) {
            $files = array_slice($files, 0, self::max_files());
        } else {
            $files = array_slice($files, 0, 1);
        }

        // ابتدا همه فایل‌ها اعتبارسنجی می‌شوند تا در صورت خطا هیچ فایلی آپلود نشود
        foreach ($files as $file) {
            $valid = $this->validate($file);
            if ($valid !== true) {
                return ['success' => false, 'urls' => [], 'url' => null, 'message' => $valid];
            }
        }

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        add_filter('upload_dir', [$this, 'coustome_upload_dir']);

        $urls = [];
        $upload_overrides = array('test_form' => false);

        foreach ($files as $file) {
            $uploaded_file = wp_handle_upload($file, $upload_overrides);

            if ($uploaded_file && !isset($uploaded_file['error'])) {
                $urls[] = $uploaded_file['url'];
            } else {
                remove_filter('upload_dir', [$this, 'coustome_upload_dir']);
                $message = isset($uploaded_file['error']) ? $uploaded_file['error'] : 'خطا در آپلود فایل';
                return ['success' => false, 'urls' => [], 'url' => null, 'message' => $message];
            }
        }

        remove_filter('upload_dir', [$this, 'coustome_upload_dir']);

        return [
            'success' => true,
            'urls'    => $urls,
            'url'     => !empty($urls) ? $urls[0] : null,
            'message' => '',
        ];
    }

    public function coustome_upload_dir($args)
    {
        $year = date("Y", time());
        $month = date("m", time());
        $coustome_dir = '/tkm-uploads' . '/' . $year . '/' . $month;
        $args['subdir'] = $coustome_dir;
        $args['path'] = $args['basedir'] . $coustome_dir;
        $args['url'] = $args['baseurl'] . $coustome_dir;

        return $args;
    }
}
