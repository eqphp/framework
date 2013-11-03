$(function(){

click('input|#result_tip','&nbsp;','tip');

$('#admin_login').submit(function(){

var act_url=$(this).attr('action');
var account=$('input[name="account"]').val();
var password=$('input[name="password"]').val();
var code=$('input[name="code"]').val();

var json={
'account':account,
'password':password,
'code':code,
'login_submit':true
};

if (account.length<2 || password.length<6) {
$('#result_tip').html('<em>账号和密码必须正确填写！</em>');
return false;
}

if (code.length!=4) {
$('#result_tip').html('<em>验证码必须正确填写！</em>');
return false;
}

$.post(act_url,json,function(data){

if (data.error==0) {
location.href=data.url;
}

var tip_arr=new Array(
'<b>登陆成功！</b>',
'<em>服务器繁忙，登陆失败！</em>',
'<em>验证码填写错误！</em>',
'<em>账号不存在！</em>',
'<em>密码错误！</em>'
);
$('#result_tip').html('<em>'+tip_arr[data.error]+'</em>');
});


return false;
});

});