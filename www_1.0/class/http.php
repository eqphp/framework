<?php
class http{

//域内跳转
static function skip($url=null){
exit(header('Location: '.dc_url.$url));
}

//域内URL跳转
static function redirect($url,$time=5,$msg=''){
$url=trim($url,'/....');

$js_script='<script language="JavaScript" type="text/javascript">';
$js_script.='var t_s='.$time.';';
$js_script.='var time=document.getElementById("skip_time");'; 
$js_script.='function time_js(){';
$js_script.='t_s--;time.innerHTML = t_s;';
$js_script.='if (t_s<=0) {location.href="http://www.baidu.com";clearInterval(inter);}}';
$js_script.='var inter=setInterval("time_js()",1000);';
$js_script.='</script>';

$msg=trim($msg).'<br>系统将在<span id="skip_time">'.$time.'</span>'.$js_script;
$msg.='秒之后自动跳转到：<br><a href="'.$url.'">'.$url.'</a>';

if (headers_sent()) {
exit('<meta http-equiv="Refresh" content="'.$time.';URL='.$url.'">'.$msg);	
}

header('refresh:'.$time.';url='.$url);
exit($msg);
}

//输出
static function out($info,$mode=true,$is_end=true){
$mode && header('Content-Type:text/html; charset=utf-8');
$is_end && exit($info);
echo $info;
}

//输出js
static function script($data=null,$type='back_refresh',$is_end=true){
$sever_referer=isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : dc_url;
if ($type=='back_refresh') return header('Location: '.$sever_referer);

if (is_array($data)) {
$js_arr['alert_skip_url']='alert("'.$data[0].'");location.href="'.$data[1].'";';
} else {
$js_arr['alert']='alert("'.$data.'");';
$js_arr['alert_back']='alert("'.$data.'");location.href="javascript:history.go(-1)";';
$js_arr['skip_url']='location.href="'.$data.'";';
$js_arr['act_js']=$data;
}

$js_script='<script language="JavaScript" type="text/javascript">';
$js_script.=$js_arr[$type].'</script>';

$is_end && exit($js_script);
echo $js_script;
}

//输出json
static function json($data,$mode=true,$is_end=true){
$mode && header('Content-Type:application/json; charset=utf-8');
$json=json_encode($data);
$is_end && exit($json);
echo $json;
}

//输出xml
static function xml($data,$tag='rss',$mode=true,$is_end=true){
$mode && header('Content-Type:text/xml; charset=utf-8');
$xml=fun::xml($data,$tag);
$is_end && exit($xml);
echo $xml;
}

//构造post提交并获取接口返回数据
static function curl($url,$data){

if (is_array($url)) {
$url=$url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'];
}

if (is_array($data)) {
$data=fun::arr_str($data);
}
$data=trim($data);

$ch=curl_init();
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
ob_start();
curl_exec($ch);
$result=ob_get_contents();
ob_end_clean();
return json_decode($result,true);
}

//以post请求发送socket并返回接口数据
static function socket($url,$data){

//解析url
if (!is_array($url)) {
$url=parse_url($url);
}

//打开socket
$fp=fsockopen($url["host"],$url["port"],$error_no,$error_info,30);
if (!$fp) return 'error:('.$error_no.')'.$error_info;

//组装发送数据
if (is_array($data)) {
$data=fun::arr_str($data);
}
$data=trim($data);

//构造头部信息
$head='POST '.$url['path']." HTTP/1.0\r\n";
$head.='Host: '.$url['host']."\r\n";
$head.='Referer: http://'.$url['host'].$url['path']."\r\n";
$head.="Content-type: application/x-www-form-urlencoded\r\n";
$head.='Content-Length: '.strlen($data)."\r\n\r\n";
$head.=$data;

//接收并返回结果
$write=fputs($fp,$head);
while (!feof($fp)) {
$info=fgets($fp);
}
return json_decode($info,true);
}

//发送http错误头信息
static function send($code=404,$is_end=true){
$status=config('http_status.php');	
if(isset($status[$code])){
header('HTTP/1.1 '.$code.' '.$status[$code]);
header('Status:'.$code.' '.$status[$code]);
$is_end && exit;
}
}

//查询http类方法
static function tip(){
$info='<br><font color="green">';
$info.='1、域内跳转：skip($url=null)<br>';
$info.='2、发送字串：out($info,$mode=false,$is_end=false)<br>';
$info.='3、输出脚本：js($data,$mode="back_refresh",$is_end=false)<br>';
$info.='4、发送json：json($data,$mode=false,$is_end=false)<br>';
$info.='5、发送xml：xml($data,$tag="rss",$mode=false,$is_end=false)<br>';
$info.='6、curl_post：curl($url,$data)<br>';
$info.='7、socket_post：socket($url,$data)<br>';
$info.='8、发送http头信息：send($code=404,$is_end=true)</font><br><br>';
return $info;
}


}