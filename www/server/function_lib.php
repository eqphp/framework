<?php

//解析配置文件
function config($name,$file='config',$dir_mode=false){
if (strpos($name,'.php')) {
$file_name=dc_root.'config/php_data/'.$name;
if ($dir_mode) $file_name=$file.$name;
return include($file_name);
}

$file_name=dc_root.'config/'.$file.'.ini';
if ($dir_mode) $file_name=$file;
$info=parse_ini_file($file_name,true);
if (!trim($name)) return $info;
$arr=explode('|',$name);
return (count($arr)==1)?$info[$arr[0]]:$info[$arr[0]][$arr[1]];
}

//获取请求(URL位段-pathinfo=>get)参数值
function rq($lie=0,$type=0){
// $lie=$lie+n; //n为相对根目录的深度
$server_uri=isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$rq_arr=explode('/',trim($server_uri,'/'));
if ($lie<count($rq_arr)) {
$value=get_magic_quotes_gpc() ? $rq_arr[$lie] : addslashes($rq_arr[$lie]);
return $type ? abs((int)$value) : strval(trim($value));
}
}

//构造sql语句
function sql($option,$from='',$where=null){
if (is_array($option)) {
$sql='';
$key_arr=array('select','from','where','group','having','order','limit');
foreach ($key_arr as $key) {
$value=($key=='group' || $key=='order')?$key.' by':$key;
$sql.=(isset($option[$key]) && trim($option[$key]))?' '.$value.' '.trim($option[$key]):'';
}
return $sql;
}

$sql='select '.$option.' from '.$from;
if ($where!==null) {
$sql.=' where '.$where;
}
return $sql;
}

//通用对象加工厂(构造函数的参数必须全部设为可选参数)
function factory($class_name,$param){
if (!is_array($param)) $param=array($param);
$new_obj=new $class_name();
call_user_func_array(array($new_obj,'__construct'),$param);
return $new_obj;
}



/*==================*/
/*****类方法简写*****/
/*==================*/

//调试方法
function out($dim='please input $dim !',$mode=0,$stop=false){
return debug::out($dim,$mode,$stop);
}

//模板配置
function smarty($group='home'){
return s_system::tpl($group);
}

//session方法
function session($key=null,$value=false){
return session::merge($key,$value);
}

//多库(改名db_many)操作获取db对象方法
function db($i=1){
return db_many::get($i);
}

//memcache方法
function mc(){
return s_memcache::many();
}

//表单值接收处理方法
function post($name,$mode='title'){
return form::post($name,$mode);
}

//数据库表解析方法
function table($name){
return s_table::table($name);
}