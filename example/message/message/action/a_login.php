<?php

class a_login{

private $static_class;

//无权限提示页面
static function forbid(){
$tpl=smarty();
$head['frame']='_self';
$head['title']='无权限提示_EQPHP案例留言本';
$tpl->assign('head',$head);

$tpl->assign('admin',session('admin'));
$tpl->display('message/forbid');
}

//输出图片验证码
static function check_code(){
$check_code=substr(md5(rand()),-4);
return pic::code($check_code);
}


//退出登陆
static function logout(){
session(null);
http::skip('message/login');
}


//登录页面
static function index(){
$admin=session('admin');

//若管理员已登录，跳转至管理首页
if ($admin && $admin['account']) {
http::skip('message/manage');
}

$tpl=smarty();
$head['title']='管理员登陆_EQPHP案例留言本';
$tpl->assign('head',$head);

//输出模板
$tpl->display('message/login');
}


static function act_login(){
if (post('login_submit','isset')) {
//检查验证码
$check_code=post('code','post');
if ($check_code!=session('login_code',true)) {
http::json(array('error'=>2,'info'=>'check_code error'));
}

//接收数据并验证
$account=post('account','account');
$password=safe::md5(post('password','post'));
self::check_admin($account,$password);
}

http::json(array('error'=>1,'info'=>'login failed'));
}

private static function check_admin($account,$password){
//用配置管理员数据（可换数据库）
$admin_account=config('admin|account','message_admin');
$account_list=explode(',',$admin_account);

if (!$account || !in_array($account,$account_list)) {
http::json(array('error'=>3,'info'=>'inexistent account'));
}

if ($password!=config('password|'.$account,'message_admin')) {
http::json(array('error'=>4,'info'=>'password error'));
}

$admin_access=config('access|'.$account,'message_admin');
session('admin',array('account'=>$account,'access'=>$admin_access));
http::json(array('error'=>0,'info'=>'login succeed','url'=>dc_url.'message/manage'));
}





}