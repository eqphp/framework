<?php

//声明message控制器
class a_index{

//由于功能简单，静态类即可实现
private $static_class;


//发表留言页面
static function index(){
$tpl=smarty();

//设置、输出页面标题和关键词
$head['title']='欢迎使用EQPHP案例留言本';
$head['keywords']='EQPHP留言本、简约留言薄、邮件发送、数据安全与过滤';
$head['keywords'].='json数据、权限分配、正则验证、配置文件读取、css sprite';
$tpl->assign('head',$head);

//渲染视图模板
$tpl->display('message/index');
}


//接收处理用户留言数据
static function act_message(){

if (post('message_submit','isset')) {
//检查验证码
$check_code=post('code','post');
if ($check_code!=session('message_code',true)) {
http::json(array('error'=>2,'info'=>'check_code error'));
}

//接收、过滤数据
$data['user_name']=post('name','title');
$data['tel']=post('contact_tel','number');
$data['phone']=post('contact_phone','account');
$data['email']=post('email','account');
$data['message']=post('message_content','info');

//验证数据
$data['tel']=safe::reg($data['tel'],'tel') ? $data['tel'] : null;
$data['phone']=safe::reg($data['phone'],'phone') ? $data['phone'] : null;
$data['email']=safe::reg($data['email'],'email') ? $data['email'] : null;

if ($data['message']) {
$add_result=db::add('message',$data); //将数据写入留言表
if ($add_result) {
http::json(array('error'=>0,'info'=>'add message succeed'));
}
}
}

//以json格式返回给浏览器
http::json(array('error'=>1,'info'=>'add message failed'));
}

}