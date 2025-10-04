<?php 

class TKM_Upload_Voice {

    public $voice;

    public function __construct($voice) {
        $this->voice = $voice;
    }

    public function upload() {
        add_filter('upload_dir', [$this, 'custom_upload_dir']);

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // مشخصات آپلود برای تست فرمی غیر از فایل (مثل ویس)
        $upload_overrides = array('test_form' => false);

        // ساخت باینری ویس
        $decoded_audio = base64_decode(preg_replace('#^data:audio/\w+;base64,#i', '', $this->voice));  // حذف اطلاعات اضافه Base64

        // ساخت مسیر و نام فایل ویس
        $file_name = 'voice_' . time() . '.webm';  // فرمت WebM برای ذخیره‌سازی انتخاب شده است
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['path'] . '/' . $file_name;
        $file_url = $upload_dir['url'] . '/' . $file_name;

        // ذخیره فایل ویس در سرور
        file_put_contents($file_path, $decoded_audio);

        if (file_exists($file_path)) {
            return ['success' => true, 'url' => $file_url];
        } else {
            return ['success' => false, 'message' => 'خطا در ذخیره‌سازی ویس'];
        }
    }

    public function custom_upload_dir($args) {
        $year = date("Y", time());
        $month = date("m", time());
        $custom_dir = '/tkm-voice-uploads' . '/' . $year . '/' . $month;
        $args['subdir'] = $custom_dir;
        $args['path'] = $args['basedir'] . $custom_dir;
        $args['url'] = $args['baseurl'] . $custom_dir;

        return $args;
    }
}