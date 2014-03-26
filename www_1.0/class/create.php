<?php

class create{

//保存为通用json数据（前端）
static function json($data,$file_name,$dir=''){
$save_dir=self::is_exist('json',$dir);
$data=json_encode($data);
return file::save($save_dir.$file_name,$data);
}


//生成静态html文件
static function html($url,$file_name,$dir=''){
$save_dir=self::is_exist('html',$dir);
$data=file_get_contents($url);
return file::save($save_dir.$file_name,$data);
}


//检查目录、文件是否存在，不存在则进行创建
private static function is_exist($app_dir,$self_dir){
$save_dir=dc_file_file.trim($app_dir,'/').'/';

if (trim($self_dir)) {
$save_dir.=trim($self_dir,'/').'/';
}

if (!file_exists($save_dir)) {
file::folder($save_dir);
}

return $save_dir;
}









}