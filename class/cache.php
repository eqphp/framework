<?php

//rely on: file help
class cache{

    private $custom_dir; //用户自定义的缓存文件目录
    private $save_dir; //真实目录
    private $file_name; //文件名
    private $ext; //扩展名
    private $expire; //有效期


    //初始化成员属性(expire:default=>30 days,0=>360 days)
    public function __construct($name = '', $dir = '', $expire = 2592000){
        $this->ext = preg_replace('/.*\./', '', $name);
        $this->custom_dir = $this->ext . '/';
        if (trim($dir)) {
            $this->custom_dir .= trim($dir, '/') . '/';
        }

        $this->save_dir = CACHE_DATA . $this->custom_dir;
        $name = strtolower(md5(strtolower(trim($name)) . 'eqphp'));
        $this->file_name = $this->save_dir . $name . '.' . $this->ext;
        $this->expire = $expire ? $expire : 31104000;
    }

    //修改、保存缓存文件(php(string/array),ini,js(json),xml,txt)
    function save($data){

        //如果缓存目录不存在则创建
        if (!$this->is_exist(false)) {
            file::folder($this->save_dir);
        }

        if ($this->ext == 'php') {
            $data = '<?php' . PHP_EOL . 'return ' . var_export($data, true) . ';';
        }

        if ($this->ext == 'json') {
            $data = json_encode($data);
        }

        if ($this->ext == 'ini') {
            $data = help::array_ini($data);
        }

        if ($this->ext == 'xml') {
            $data = '<?xml version="1.0" encoding="utf-8"?>';
            $data .= '<data>' . help::data_xml($data) . '</data>';
        }

        if (is_array($data) && ($this->ext == 'txt')) {
            $data = serialize($data);
        }

        return file_write($this->file_name, $data);
    }

    //获取缓存文件(php/ini/json/xml)
    function get($option_name = null){
        if ($this->is_exist() && $this->is_expire()) {

            if ($this->ext == 'php') {
                $data = include($this->file_name);
                return array_get($data, $option_name);
            }

            if ($this->ext == 'json') {
                $data = json_decode(file_get_contents($this->file_name), true);
                return array_get($data, $option_name);
            }

            if ($this->ext == 'ini') {
                $data = parse_ini_file($this->file_name);
                return array_get($data, $option_name);
            }

            if ($this->ext == 'xml') {
                return simplexml_load_file($this->file_name);
            }

            if ($this->ext == 'txt') {
                $data = file_get_contents($this->file_name);
                if ($option_name) {
                    return unserialize($data);
                }
                return $data;
            }

        }
    }

    //清除缓存文件
    function clear($is_clear_dir = false){
        if ($this->is_exist()) {
            file::delete($this->file_name);
        }
        if ($is_clear_dir && $this->is_exist(false)) {
            file::delete($this->save_dir, true);
        }
    }

    //检测文件是否已过有效期
    private function is_expire(){
        $file_save_time = intval(filemtime($this->file_name));
        if (($file_save_time + $this->expire) > time()) {
            return true;
        }
    }

    //检测文件、文件目录是否存在
    private function is_exist($is_check_file = true){
        if ($is_check_file) {
            return file_exists($this->file_name);
        }
        return file_exists($this->save_dir);
    }


}