system_domain='127.0.0.1';
system_url='http://'+system_domain+'/';

//正则表达式
reg_exp={
'tel':/^((\(\d{3}\))|(\d{0}))?(13|14|15|18)\d{9}$/,
'email':/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/,
'phone':/^(0\d{2,3})?-?([2-9]\d{6,7})(-\d{1,5})?$/,
'phone_48':/^(400|800)?-?\d{7}(-\d{1,5})?$/,
'qq':/^[1-9]\d{4,9}$/,
'name':/^[a-zA-Z][a-zA-Z0-9_]{4,17}$/,
'pwd':/^(.){6,15}$/,
'money':/^[0-9]+([.]{1}[0-9]{1,2})?$/,
'number':/^[0-9]*[1-9][0-9]*$/,
'url':/^http(s?):\/\/([\w-]+\.)+[\w-]+(\/[\\w\- \.\/?%&=]*)?/,
'cid':/^\d{18}\d{15}/,
'zip':/^\d{6}$/,
'address':/^(.){0,50}$/,
'require':/.+/,
'int':/^[-\+]?\d+$/,
'float':/^[-\+]?\d+(\.\d+)?$/,
'english':/^[A-Za-z]+$/,
'name_cn':/^[\u4E00-\u9FA5]{2,4}$/,
'account':/^[\u4E00-\u9FA5\uf900-\ufa2d\w]{5,16}$/
};

//图片等比例缩放
function set_size(obj_id,_w,_h){
var wh=($(obj_id+" img").width()-_w)/$(obj_id+" img").width();
var ht=($(obj_id+" img").height()-_h)/$(obj_id+" img").height();

if (wh>ht) {

if(wh>0){
$(obj_id+" img").attr("width",_w); 
$(obj_id+" div").attr("width",_w);
}

} else {

if(ht>0){
$(obj_id+" img").attr("height",_h); 
$(obj_id+" div").attr("height",_h);
}

}

var m_top=(_h-$(obj_id+" img").height())/2;
var m_left=(_w-$(obj_id+" img").width())/2;
$(obj_id+" #img_div").css("margin",m_top+"px auto auto "+m_left+"px");
$(obj_id+" img").css("display","block");
}