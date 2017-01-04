<?php

class upload{

    protected $file = array();
    protected $format, $from;
    protected $save_name, $save_directory, $save_path;

    static $file_error = array('upload successful', 'greater than server allow size',
        'greater than client allow size', 'only upload part', 'please select upload file');

    function __construct($file, $file_name, $allow_size = array(1), $from = 'system'){
        $this->file = $file;
        $this->save_name = basename($file_name);
        $this->from = $from;
        $this->save_directory = trim($file_name,$this->save_name);
        $this->check_file();

        if (is_array($allow_size)) {
            $this->check_size($allow_size[0]);
            $this->image_size = $allow_size[1];
        } else {
            $this->check_size($allow_size);
        }

        $this->check_format();
        $this->check_directory();
        $this->move();
    }

    //检测上传文件
    function check_file(){
        if (empty($this->file)) {
            throw new sException('greater than upload_max_filesize', 120, 1);
        }
        if (is_uploaded_file($this->file['tmp_name'])) {
            if ($this->file['error']) {
                $error_message = self::$file_error[$this->file['error']];
                throw new sException($error_message, $this->file['error'] + 120, $this->file['error'] + 1);
            }
            if ($this->file['size']) {
                preg_match('|\.(\w+)$|', $this->file['name'], $extension_name);
                $this->file['extension_name'] = strtolower(trim($extension_name[0], '.'));
                if (in_array($this->file['extension_name'], config('system.picture', 'upload'))) {
                    $this->process_image_size();
                    if (isset($this->image_size)) {
                        $this->check_image_size();
                    }
                }
                return true;
            }
            throw new sException('please select upload file', 125, 6);
        }
        throw new sException('no file upload', 126, 7);
    }

    //检测文件体积
    function check_size($allow_size){
        if ($this->file['size'] <= $allow_size * 1048576) {
            return true;
        }
        throw new sException('greater than allow upload size', 127, 8);
    }


    //检测上传格式
    function check_format(){
        if (isset($this->file['mime_type']) && $this->file['mime_type'] !== $this->file['type']) {
            throw new sException('fake extension name', 128, 9);
        }
        $this->format = config($this->from, 'upload');
        foreach ($this->format as $path => $format) {
            if (in_array($this->file['extension_name'], $format)) {
                $this->save_path = PATH_FILE . $path . '/';
                return true;
            }
            throw new sException('allow format ' . implode(',', $format), 129, 10);
        }
    }

    //检测上传目录
    function check_directory(){
        file_exists($this->save_path) or mkdir($this->save_path);
        if (is_dir($this->save_path)) {
            if (is_writable($this->save_path)) {
                return true;
            }
            throw new sException('absent directory ' . $this->save_path, 130, 11);
        }
        throw new sException('unable to write directory ' . $this->save_path, 131, 12);
    }

    //移动到指定存放目录
    function move(){
        if ($this->save_directory) {
            $this->save_path .= $this->save_directory;
            $this->check_directory();
        }

        if (!regexp::match($this->save_name, 'file_name')) {
            throw new sException('file name format error', 132, 13);
        }

        $file_name = $this->save_path . $this->save_name . '.' . $this->file['extension_name'];
        if (move_uploaded_file($this->file['tmp_name'], $file_name)) {
            $this->file['file_name'] = $file_name;
            chmod($file_name, 0644);
            return $file_name;
        }
        throw new sException('move file fail', 133, 14);
    }

    function get($key = 'file_name'){
        if (isset($this->file[$key])) {
            return $this->file[$key];
        }
    }

    //处理图片尺寸
    private function process_image_size(){
        $image = getimagesize($this->file['tmp_name']);
        $this->file['width'] = isset($image[0]) ? $image[0] : 0;
        $this->file['height'] = isset($image[1]) ? $image[1] : 0;
        $this->file['mime_type'] = isset($image['mime']) ? $image['mime'] : '';
    }

    //校验图片宽高
    private function check_image_size(){
        list($min_width, $max_width, $min_height, $max_height) = $this->image_size;
        if ($min_width && $this->file['width'] < $min_width) {
            throw new sException('less than allow image width', 134, 15);
        }
        if ($max_width && $this->file['width'] > $max_width) {
            throw new sException('greater than allow image width', 135, 16);
        }
        if ($min_height && $this->file['width'] < $min_height) {
            throw new sException('less than allow image height', 136, 17);
        }
        if ($max_height && $this->file['width'] > $max_height) {
            throw new sException('greater than allow image height', 137, 18);
        }
    }

}