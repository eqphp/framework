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
$(form_option.split('|')[0]).live('keyup',function(){
//判断按键值是否正确
if (mode=='check_value') {
if ($(this).val()=='') {/***待写****/}
}

//提示信息（计算总价）
if (mode=='count_num') {
var now_num=parseInt($(this).val());
var now_result=isNaN(now_num) ? 1 : now_num;
$(this).val(now_result);
$(form_option.split('|')[1]).val(now_num*act_info);
}

//提示信息（如还可输入字数）
if (mode=='tip_num') {
var now_value=$.trim($(this).val());
var char_num=now_value.length;
var min_num=parseInt(act_info.split('|')[0]);
var max_num=parseInt(act_info.split('|')[1]);
var allow_num,tip_str;
if (char_num<min_num) {
allow_num=min_num-char_num;
tip_str='您还需输入：<em>'+allow_num+'</em>个字';
} else {

if (char_num==max_num) {
tip_str='刚好<i>'+max_num+'</i>个字';
} else {
var allow_num=max_num-char_num;
tip_str='您还可以输入：<i>'+allow_num+'</i>个字';
}

}

if (char_num>max_num) {
$(this).val(now_value.substr(0,max_num));
tip_str='不过所允许的字数：<em>'+max_num+'</em>';
}

$(form_option.split('|')[1]).html(tip_str);
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