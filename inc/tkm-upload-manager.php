<?php 

defined('ABSPATH') || exit('NO Access');

class TKM_Upload_File {

    public $file;

    public function __construct($file) {
        $this->file = $file;    
    }

    public function upload() {
        add_filter('upload_dir', [$this, 'coustome_upload_dir']);

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload_overrides = array('test_form' => false);
        $uploade_file = wp_handle_upload($this->file, $upload_overrides);

        if ($uploade_file && !isset($uploade_file['error'])) {
            return ['success' => true, 'url' => $uploade_file['url']];
        } else {
            return ['success' => false, 'message' => $uploade_file['error']];
        }
    }

    public function coustome_upload_dir($args) {
        $year = date("Y", time());
        $month = date("m", time());
        $coustome_dir = '/tkm-uploads' . '/' . $year . '/' . $month;
        $args['subdir'] = $coustome_dir;
        $args['path'] = $args['basedir'] . $coustome_dir;
        $args['url'] = $args['baseurl'] . $coustome_dir;

        return $args;
    }
}