<?php

class a_index{

// 静态类(yes)
private $static_class;


//首页
static function index(){
$tpl=smarty();
$head['title']='Home';
$tpl->assign('head',$head);
self::logo();
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
$dir=dc_cache_cache.'smarty/compile/';
file::del($dir,false);
http::out('清理完毕');
}


}