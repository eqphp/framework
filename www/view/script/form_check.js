//点击
function click(form_option,act_info,mode){
$(form_option.split('|')[0]).click(function(){

//提示、并清空
if (mode=='tip_clear') {
$(this).val('');
$(form_option.split('|')[1]).html('');
}

//清空表单项/判断若相等则清空
if ((mode=='clear') || ((mode=='eq_clear') && ($(this).val()==act_info))) {
$(this).val('');
}

//显示提示信息
if (mode=='tip') {
$(form_option.split('|')[1]).html(act_info);
}

});
}

//按下按键
function keydown(form_option){
$(form_option).keydown(function(){
//清空
if (mode=='clear') {
$(this).val();
}
});
}

//松开按键
function keyup(form_option,act_info,mode){
$(form_option.split('|')[0]).keyup(function(){
//判断按键值是否正确
if (mode=='check_value') {
if ($(this).val()=='') {/***待写****/}
}

//提示信息（计算总价等）
if (mode=='count_num') {
var now_num=parseInt($(this).val());
var now_result=isNaN(now_num) ? 0 : now_num;
$(form_option.split('|')[1]).val(now_num*act_info);
}

//提示信息（如还可输入字数）
if (mode=='tip_num') {
var char_num=$(this).val().length;
var allow_num=act_info-char_num;
$(form_option.split('|')[1]).html('您还可以输入：<em>'+allow_num+'</em>个字');
}

});
}

//改变
function change(form_option,act_info){
$(form_option.split('|')[0]).change(function(){
if ($(this).val()=='') {
$(form_option.split('|')[1]).html(act_info.split("|")[0]);
} else {
$(form_option.split('|')[1]).html(act_info.split("|")[1]);
}
});
}

//选择
function select(form_option,act_info,mode){
$(form_option.split('|')[0]).select(function(){
//提示
if (mode=='tip') {
$(form_option.split('|')[1]).html(act_info);
}
//禁止选择
if (mode=='forbid') {
return false;
}
//禁止、并提示
if (mode=='forbid_tip') {
$(form_option.split('|')[1]).html(act_info);
return false;
}
});
}

//失焦
function blur(form_option,act_info,length,step){
$(form_option.split('|')[0]).blur(function(){
//最小长度，非空验证
if ($(this).val().length<length) {
$(form_option.split('|')[1]).html(act_info.split("|")[0]);
} else {
if (step<2) {
$(form_option.split('|')[1]).html(act_info.split("|")[0]);
return false;
}
//是否含有字母
if (!isNaN($(this).val())) {
$(form_option.split('|')[1]).html(act_info.split("|")[1]);
} else {
if (step<3) {
$(form_option.split('|')[1]).html(act_info.split("|")[1]);
return false;
}
//是否含有汉字
if (escape($(this).val()).indexOf("%u")==-1) {
$(form_option.split('|')[1]).html(act_info.split("|")[2]);
} else {
if (step<4) {
$(form_option.split('|')[1]).html(act_info.split("|")[2]);
return false;
}
$(form_option.split('|')[1]).html(act_info.split("|")[3]);
}
}
}
});
}

//正则验证
function regular(form_option,act_info,regular){
$(form_option.split('|')[0]).blur(function(){
if (regular.test($(this).val())) {
$(form_option.split('|')[1]).html(act_info.split("|")[1]);
} else {
$(form_option.split('|')[1]).html(act_info.split("|")[0]);
}
});
}