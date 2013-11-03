$(function(){
$("#wait_icon").css("display","none");

//判断是否是chrome浏览器上传
if (navigator.userAgent.toLowerCase().match(/chrome/) != null) {
$('#up_file').click(function(){
this.focus();
});
}

//上传
$("#up_file").blur(function(){
if($(this).val()) {
$("#form_up").submit();
$(this).css("display","none");
$("#wait_icon").css("display","block");
} else {
return false;
}
});
});