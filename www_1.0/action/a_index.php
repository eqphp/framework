<?php

class a_index{

// 静态类(yes)
private $static_class;


//首页
static function index(){

//初始化工作
self::del_empty_file('gitkeep'); //1、删除空文件（github空目录）
self::logo(); //2、生成logo图片
self::clean(); //3、删除模板编译文件



$tpl=smarty();
$head['title']='Home';
$tpl->assign('head',$head);
$tpl->assign('logo','<img src="'.dc_url_create.'eqphp_logo.png">');
cookie::set('frame_name','EQPHP');
$tpl->display('index');
}


//解析框架ICON标志
static function logo(){
//header("Content-type:image/gif");
$logo_file=dc_data_static.'txt/logo_pic.txt';
$res=base64_decode(file_get_contents($logo_file));
file_put_contents(dc_file_create.'eqphp_logo.png',$res);
//echo $res;
}


//清理smarty模板编译文件
static function clean(){
file::del(dc_cache_cache.'smarty/compile/',false);
file::del(dc_cache_cache.'compile/',false);
}

//删除空文件（github空目录）
private static function del_empty_file($ext_name='gitkeep'){
file::scan(dc_root,$ext_name,$file_list);
if (count($file_list)<1) return true; 
foreach ($file_list as $file) {
file::del($file);
}

}




}