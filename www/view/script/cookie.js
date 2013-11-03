//设定cookie值
function set_cookie(name,value){
var expdate=new Date();
var argv=set_cookie.arguments;
var argc=set_cookie.arguments.length;
var expires=(argc>2)?argv[2]:2592000; //30 days
var path=(argc>3)?argv[3]:'/';
var domain=(argc>4)?argv[4]:system_domain;
var secure=(argc>5)?argv[5]:false;
if (expires!=null) expdate.setTime(expdate.getTime()+(expires*1000));
document.cookie=name+"="+escape(value)+((expires==null)?"":("; expires="+expdate.toGMTString()))+((path==null)?"":("; path="+path))+((domain==null)?"":("; domain="+domain))+((secure==true)?"; secure":"");
}

//获得cookie的原始值
function get_cookie(name){
var arg=name+"=";
var alen=arg.length;
var clen=document.cookie.length;
var i=0;
while (i<clen){
var j=i+alen;
if (document.cookie.substring(i,j)==arg)
return get_cookie_val(j);
i=document.cookie.indexOf(" ",i)+1;
if (i==0) break;
}
return null;
}

//删除cookie
function del_cookie(name){
var exp=new Date();
exp.setTime(exp.getTime()-1);
var cval=get_cookie(name);
document.cookie=name+"="+cval+"; expires="+exp.toGMTString();
}

//获得cookie解码后的值
function get_cookie_val(offset){
var endstr=document.cookie.indexOf(";",offset);
if (endstr==-1)
endstr=document.cookie.length;
return unescape(document.cookie.substring(offset,endstr));
}