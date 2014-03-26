$(function(){

$('.main p:eq(0)').show();
get_count_info(1);

$('.nav_title').mouseover(function(){
$('.nav_title dl dd').show();
}).mouseleave(function(){
$('.nav_title dl dd').hide();
});

$('.nav_title dl dd').each(function(i){
$(this).click(function(){
$('.nav_title dl dd').css('color','#005EAC');
$(this).css('color','red');

var title=new Array(
'EQPHP简介',
'在哪儿可以获取EQPHP？',
'安装与配置',
'命名规范与相关约束',
'案例：留言本',
'技术团队与社区',
'目录结构与说明',
'调用流程与执行原理',
'控制器（action）',
'视图（view）',
'模型（model）与业务逻辑（server）',
'组件（plugin）',
'工具类（class）、函数方法（function_lib）',
'数据库操作（DB）',
'请求与响应（request & response）',
'表单处理与数据安全（form & data security）',
'文件操作、上传（file & upload）',
'cookie与session',
'缓存与静态化(cache & static)',
'加密、图像、邮件(encrypt/image/mail)',
'日志、性能、错误调试(log/debug)'
);
$('.main h1 strong').html(title[i]);

get_count_info(i+1);
$('.main h1').attr('article_id',i+1);
$('.main p').hide();
$('.main p:eq('+i+')').show();
}).mouseover(function(){

var now_color=$(this).css("color");
if (now_color=='rgb(0, 94, 172)' || now_color=='#005eac') {
$(this).css('color','#FE5F1B');
}

}).mouseout(function(){

var now_color=$(this).css("color");
if (now_color=='rgb(254, 95, 27)' || now_color=='#fe5f1b') {
$(this).css('color','#005EAC');
}

});
});




$('.main h1 a').each(function(j){
$(this).click(function(){

var article_id=$('.main h1').attr('article_id');

if (j<2) { //投票
var vote_list=get_cookie('vote_list');
var now_vote=parseInt($(this).html());
var now_object=$(this);

if (vote_list===null) {
set_cookie('vote_list',article_id);
vote(now_object,now_vote,article_id,j);

} else {

var vote_option=vote_list.split(',');
if (in_array(article_id,vote_option)) {
var tip=new Array('您已经投过票了','哇塞，不用这么勤快！','请给其它资源投票！');
var i=get_random(3);
$('#vote_tip').html(tip[i]).show().fadeOut(3000);
} else {
set_cookie('vote_list',vote_list+','+article_id);
vote(now_object,now_vote,article_id,j);
}
}
}

if (j===2) { //评论
$('#comment_data').html('&nbsp;');
get_comment(article_id,1);

$('#comment').show();
$("html,body").animate({scrollTop:$('#comment').offset().top},1000);
}


if (j===3) { //文章

}


});




});


//加载更多评论
$('#load_more button').click(function(){
var now_page=parseInt($(this).attr('page'));
$(this).attr('page',now_page+1);

if (now_page==0) {
$("html,body").animate({scrollTop:$('#comment').offset().top},1000);
$('#load_more').hide();
} else {
var article_id=$('.main h1').attr('article_id');
get_comment(article_id,now_page+1);
}
});



//提交评论
keyup('.comment_textarea|.pub_result_tip','3|300','tip_num');
$('#comment_form').submit(function(){
var tip_object=$('.pub_result_tip');
var article_id=$('.main h1').attr('article_id');
var comment=$('.comment_textarea').val();
var json={'article_id':article_id,'comment':comment};

if (comment.length<3) {
tip_object.html('<span class="fcr">评论内容不能少于3个字！</span>');
return false;
}

$.post(system_url+'manual/comment/',json,function(data){

var tip=new Array('非常感谢您的评论！','系统繁忙','请登录','评论不能为空','不可以给自己文章评论');
if (data.error==0) {
var comment_num=parseInt($('.main h1 a:eq(2)').html());
$('.main h1 a:eq(2)').html(comment_num+1);
$('.comment_textarea').val('');
tip_object.html('<span class="fcg">'+tip[data.error]+'</span>');

$('#comment_data').append(data.info);

return false;
}

if (data.error==2) {
var now_height=$("body").css("height");
$('.big_lay').css('height',now_height).show();
// return false;
}

tip_object.html('<span class="fcr">'+tip[data.error]+'</span>');
},"json");

return false;
});

//赞评论
$('.praise_btn').live('click',function(){

var praise_list=get_cookie('praise_list');
var now_praise=parseInt($(this).html());
var comment_id=$(this).attr('comment_id');
var now_object=$(this);

if (praise_list===null) {
set_cookie('praise_list',comment_id);
praise(now_object,now_praise,comment_id);

} else {

var praise_option=praise_list.split(',');
if (in_array(comment_id,praise_option)) {
now_object.css('color','red').html('您已赞过');
} else {
set_cookie('praise_list',praise_list+','+comment_id);
praise(now_object,now_praise,comment_id);
}
}
}).live('mouseout',function(){
var now_praise=$(this).attr('vote_value');
$(this).show().text(now_praise);
});

//回复评论
keyup('.reply_textarea|.reply_result_tip','1|140','tip_num');
$('.reply_btn').live('click',function(){
var now_object=$(this);
var i=$('.reply_btn').index($(this));
$('.reply_td').html('');
$('.reply_result_tip').html('140字以内神回复');
$('.reply_td:eq('+i+')').html($('#reply_form_html').html());

$('#reply_form').submit(function(){
var reply_info=$.trim($('.reply_textarea').val());
if (reply_info.length<1){
$('.reply_result_tip').html('<span class="fcr">回复内容不能为空</span>');
return false;
}
var comment_id=now_object.attr('comment_id');
var receiver=now_object.attr('user_id');
var json={'comment_id':comment_id,'receiver':receiver,'reply':reply_info};
$.post(system_url+'manual/reply/',json,function(data){
var tip=new Array('感谢您的回复','系统繁忙','请登录','操作错误','不能给自己回复');
if (data.error==0) {
$('#reply_data_'+comment_id).append(data.info);
$('.reply_textarea').val('');
$('.reply_result_tip').html('<span class="fcg">'+tip[data.error]+'</span>');
} else {

if (data.error==2) {
var now_height=$("body").css("height");
$('.big_lay').css('height',now_height).show();
// return false;
}

$('.reply_result_tip').html('<span class="fcr">'+tip[data.error]+'</span>');
}
},"json");
return false;
});

});








//用户登录、注册
set_layer('.info_lay h5 cite','.big_lay','hide');
set_layer('#login_register_btn','.big_lay','show');
like_a_icon('.info_lay h5 cite','center 0','center -24px');
like_a_btn('.info_lay img');

//login <-> register
$('.info_lay ol li').click(function(){
$('.info_lay ol li').attr('class','');
$(this).attr('class','now_option');

$('#account_tip').html('请输入您的 QQ号 或 Email');
$('#password_tip').html('6位以上任意字符');


$('input[name="password"]').val('');
var now_index=$('.info_lay ol li').index($(this));
if (now_index==1) {
$('.login_option').hide();
$('input[name="mode"]').val('register');
} else {
$('#register_succeed').hide();
$('.login_option').show();
$('input[name="mode"]').val('login');
}
});

//表单项验证
var account_option='input[name="account"]|#account_tip';
click(account_option,'<i>请输入您的 QQ号 或 Email</i>','tip');
$('input[name="account"]').blur(function(){
if (!reg_exp.email.test($(this).val()) && !reg_exp.qq.test($(this).val())) {
$('#account_tip').html('<em>目前只允许QQ号和Email</em>');
return false;
}

if ($('input[name="mode"]').val()=='register') {

$.post(system_url+'register/exist',{'account':$(this).val()},function(data){
if (data.error==0) {
$('#account_tip').html('<b>√ 正确</b>');
} else {
$('#account_tip').html('<em>已被注册，请更换</em>');
}
return false;
},'json');


} else {
$('#account_tip').html('<b>√ 正确</b>');
}


});


var password_option='input[name="password"]|#password_tip';
click(password_option,'<i>请输入6位以上任意字符</i>','tip');
blur(password_option,'<em>密码必须是6位以上任意字符</em>|<b>√ 正确</b>',6,2);

$('#login_register').submit(function(){
var mode=$('input[name="mode"]').val();
var account=$('input[name="account"]').val();
var password=$('input[name="password"]').val();
var auto_login=($("input[name='auto_login']").attr("checked")=='checked') ? 1 : 0;

if (password.length<6) {
$('#password_tip').html('<em>密码不能少于6位</em>');
return false;
}

if (reg_exp.email.test(account) || reg_exp.qq.test(account)) {

if (mode==='login') {
var json={'account':account,'password':password,'auto_login':auto_login};
var tip=new Array('登录成功','服务器繁忙，请稍后再试','账号或密码长度错误','验证码错误','账号错误','账号不存在','密码错误','账号未认证','账号已被冻结','已注销的账号','已删除的账号');
} else {
var json={'account':account,'password':password};
var tip=new Array('注册成功','服务器繁忙，注册失败','账号或密码长度错误','验证码错误','账号错误','该账号已被注册，请更换');
}

$.post(system_url+mode,json,function(data){
if (data.error==0) {

$('#account_tip').html('<b>'+tip[data.error]+'</b>');
if (mode==='login') {
$('.big_lay').fadeOut(3000);
} else {
$('#register_succeed strong').html(data.info);
$('#register_succeed').show();
}


} else {
if ((data.error==6) &&(mode==='login')) {
$('#password_tip').html('<em>'+tip[data.error]+'</em>');
} else {
$('#account_tip').html('<em>'+tip[data.error]+'</em>');
}
}

return false;
},'json');


} else {
$('#account_tip').html('<em>请填写您的 QQ号 或 Email</em>');
return false;
}


return false;
});



});

//投票
function vote(now_object,now_vote,article_id,attitude){
var json={'id':article_id,'attitude':attitude};
$.post(system_url+'manual/vote/',json,function(data){
now_object.html(now_vote+1);
var tip=new Array('谢谢专家，我们会倍加进取！','感谢支持，我们会再接再励！');
$('#vote_tip').html(tip[attitude]).css('color','green').show().fadeOut(3000);
},"json");
}

//赞
function praise(now_object,now_praise,comment_id){
$.post(system_url+'manual/praise/',{'comment_id':comment_id},function(data){
now_object.html(now_praise+1).attr('vote_value',now_praise+1);
},"json");
}

//获取评论
function get_comment(article_id,page){
$.get(system_url+'manual/get_comment/'+article_id+'/'+page,'',function(data){
if (data.page<2) {
$('#load_more').hide();
}
if (data.page) {
if (data.page>1) {
$('#load_more').show();
}
var page_mark=parseInt($('#load_more button').attr('page'));
if (page_mark==data.page) {
$('#load_more button').attr('page',0);
$('#load_more button').html('返回评论顶部↑');
} else {
$('#load_more button').html('加载更多评论↓');
}
$('#comment_data').append(data.info);
}
},"json");
}


function get_count_info(article_id){
$.post(system_url+'manual/get_vote/',{'id':article_id},function(data){
$('.main h1 a').each(function(i){
$(this).html(data[i]);
if (data[4]==0) {
$('#login_register_btn').show();
$('#top_user_info').hide();
} else {
$('#login_register_btn').hide();
$('#top_user_info b').html(data[4]);
$('#top_user_info').show();
}
});
$('#visit_tally').html(data[5]);
},"json");
}