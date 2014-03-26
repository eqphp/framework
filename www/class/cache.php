<?php

class cache{

    private $custom_dir; //用户自定义的缓存文件目录
    private $save_dir; //真实目录
    private $file_name; //文件名
    private $ext; //扩展名
    private $valid; //有效期


    //初始化成员属性
    public function __construct($name='',$dir='',$valid=2592000){
        $temp_arr=explode('.',$name);
        $this->ext=end($temp_arr);

        $this->custom_dir=$this->ext.'/';
        if (trim($dir)) {
            $this->custom_dir.=trim($dir,'/').'/';
        }

        $this->save_dir=dc_cache_db.$this->custom_dir;

        $this->file_name=$this->save_dir.self::safe($name).'.'.$this->ext;

        $this->valid=$valid ? $valid : 93312000;
    }

    //修改、保存缓存文件(php(string/array),ini,js(json),xml,txt)
    function save($data){

        //如果缓存目录不存在则创建
        if (!$this->is_exist(false)) {
            file::folder($this->save_dir);
        }

        if ($this->ext == 'php') {
            $data='<?php'."\r\n".'return '.var_export($data,true).';';
        }

        if ($this->ext == 'js') {
            $data=json_encode($data);
        }

        if ($this->ext == 'ini') {
            $data=fun::arr_ini($data);
        }

        if ($this->ext == 'xml') {
            $data=fun::xml($data);
        }

        if (is_array($data) && ($this->ext == 'txt')) {
            $data=serialize($data);
        }

        return file::save($this->file_name,$data);
    }

    //获取缓存文件(php/ini/json/xml)
    //$name参数在获取ini配置文件时启用
    function get($option_name=null){
        if ($this->is_exist() && $this->is_valid()) {

            if ($this->ext == 'php') {
                return require_once $this->file_name;
            }

            if ($this->ext == 'js') {
                return json_decode(file_get_contents($this->file_name),true);
            }

            if ($this->ext == 'ini') {
                return config($option_name,$this->file_name,true);
            }

            if ($this->ext == 'xml') {
                return simplexml_load_file($this->file_name);
            }

            if ($this->ext == 'txt') {
                $data=file_get_contents($this->file_name);
                if ($option_name) return unserialize($data);
                return $data;
            }

        }
    }

    //清除缓存文件
    function clear($mode=false){
        if ($this->is_exist()) { //如果文件存在，删除文件
            file::del($this->file_name);
        }

        //删除目录
        if ($mode && $this->is_exist(false)) {
            file::del($this->save_dir,true);
        }

    }

    //检测文件是否已过有效期
    private function is_valid(){
        $file_save_time=intval(filemtime($this->file_name));
        if (($file_save_time+$this->valid) > time()) {
            return true;
        }
    }

    //检测文件、文件目录是否存在
    private function is_exist($mode=true){
        if ($mode) { //文件
            return file_exists($this->file_name);
        } else { //文件目录
            return file_exists($this->save_dir);
        }
    }

    //产生安全的缓存文件名（不含目录）
    private static function safe($name){
        return strtolower(md5(strtolower(trim($name)).'eqphp'));
    }


    //对外方法提示
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、缓存数据：save($data)<br>';
        $info.='2、获取缓存：get($option_name=null)<br>';
        $info.='3、清除缓存：clear()</font><br><br>';
        return $info;
    }


}