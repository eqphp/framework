$(function(){
var name_option='input[name="name"]|#name_tip';
click(name_option,'<i>请填写你的真实姓名</i>','tip');
blur(name_option,'<em>不能少于2个字</em>|<em>不能全是数字</em>|<b>√</b>',2,3);

var message_option='textarea|#message_tip';
click(message_option,'<i>请填写您的留言内容</i>','tip');
blur(message_option,'<em>留言内容不能少于5个字</em>|<cite>请正确填写,谢谢</cite>|<b>√</b>',5,3);

var tel_option='input[name="contact_tel"]|#tel_tip';
click(tel_option,'<i>请填写您的现用手机号</i>','tip');
regular(tel_option,'<em>手机号填写有误</em>|<b>√</b>',reg_exp.tel);

var phone_option='input[name="contact_phone"]|#phone_tip';
click(phone_option,'<i>请填写您的固定电话号码</i>','tip');
regular(phone_option,'<em>固定电话填写有误</em>|<b>√</b>',reg_exp.phone);

var email_option='input[name="email"]|#email_tip';
click(email_option,'<i>请填写您的常用邮箱</i>','tip');
regular(email_option,'<em>邮箱填写错误</em>|<b>√</b>',reg_exp.email);

var result_option='input|#result_tip';
click(result_option,'','tip');

$('#message').submit(function(){

var act_url=$(this).attr('action');

var name=$('input[name="name"]').val();
var message_content=$('textarea').val();
var contact_tel=$('input[name="contact_tel"]').val();
var contact_phone=$('input[name="contact_phone"]').val();
var email=$('input[name="email"]').val();
var code=$('input[name="code"]').val();

var json={
'name':name,
'message_content':message_content,
'contact_tel':contact_tel,
'contact_phone':contact_phone,
'email':email,
'code':code,
'message_submit':true
};

if (name.length<2 || message_content.length<5) {
$('#result_tip').html('<em>姓名和留言内容必须正确填写！</em>');
return false;
}

if (code.length!=4) {
$('#result_tip').html('<em>验证码必须正确填写！</em>');
return false;
}

$.post(act_url,json,function(data){
var tip_arr=new Array(
'<b>非常感谢您的意见和建议，我们会尽快给你答复！</b>',
'<em>非常抱歉，服务器繁忙！</em>',
'<em>验证码填写错误！</em>'
);
$('#result_tip').html('<em>'+tip_arr[data.error]+'</em>');
});


return false;
});

});