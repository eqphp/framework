<?php

class a_act_manage extends a_access{

const cc_access=2; //设置本页面权限
const cc_access_email=3; //邮件回复权限

function del(){
//验证权限，跳转提示页面
if (!in_array(parent::del_access,$this->admin_access)) {
http::skip('login/forbid');
}

$message_id=rq(3,1);
db::del(parent::table,$message_id);
http::script(null,'back_refresh');
}



//标注为已读留言
function label_read(){
//验证权限，跳转提示页面
if (!in_array(parent::visite_access,$this->admin_access)) {
http::skip('login/forbid');
}

$message_id=rq(3,1);
$data['is_view']=1;
db::mod(parent::table,$data,$message_id);
http::script(null,'back_refresh');
}


//邮件回复
function email_reply(){
//验证权限，跳转提示页面
if (!in_array(parent::reply_access,$this->admin_access)) {
http::skip('login/forbid');
}
$tip_info=array('error'=>1,'info'=>'send email failed');
if (post('email','isset')) {
//接收数据
$email=post('email','post');
$title=post('title','title');
$content=post('content','info');

//发送邮件
mail::send($email,$title,$content);
$tip_info=array('error'=>0,'info'=>'email sent');
}
http::json($tip_info);
}




}