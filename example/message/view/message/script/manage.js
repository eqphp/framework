$(function(){

like_a_bg('.message_content tbody tr','white','#FAF0B4','#FAFFD2');

//查看留言详细内容
set_layer('#message_detail h3 cite','#message_detail','hide');
$('.message_content table tr td i').click(function(){
$('#message_detail h3 span').html($(this).parent().attr('name'));
$('#message_detail p').html($(this).parent().attr('message'));
$('#message_detail').css('top','45px').show();
});

//邮件回复层
set_layer('#email_reply h3 cite','#email_reply','hide');
$('.message_content table tr td cite').click(function(){
$('#email_reply h3 span').html('@'+$(this).attr('name'));
$('input[name="email"]').val($(this).attr('email'));
$('#email_reply').css({'top':'45px','z-index':'120'}).show();
});


//ajax发送邮件
$('#reply_email').submit(function(){

var act_url=$(this).attr('action');
var email=$('input[name="email"]').val();
var title=$('input[name="title"]').val();
var content=$('textarea[name="content"]').val();


var json={
'email':email,
'title':title,
'content':content,
'reply_btn':true
};

if (title.length<2 || content.length<3) {
$('#result_tip').html('<em>主题和内容必须正确填写！</em>');
return false;
}

$.post(act_url,json,function(data){
if (data.error==0) {
$('#result_tip').html('<b>邮件已成功发送！</b>');
} else {
$('#result_tip').html('<em>邮件发送失败！</em>');
}
});

return false;
});



});