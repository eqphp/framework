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

//判断指定值是否在指定的数组中
function in_array(value,option){
var value_type=typeof value;
var option_type=typeof option;
if (option_type!=='object') return false;
if (value_type==='number' || value_type==='string') {
for (i=0;i<option.length;i++) {
if (value===option[i]) {
return true;
break;
}
}
}
return false;
}
