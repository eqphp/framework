<?php
class fun{

//字符截取
static function str_cut($str,$length,$from=0){
$str_len=mb_strlen($str,'gb2312');
$add_str=($str_len>$length) ? '…' : '';
$exp='#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}';
$exp.='((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$length.'}).*#s';
return preg_replace($exp,'$1',$str).$add_str; 
}


//将数组拆分成指定的（键、值）数组
static function kv_arr($data,&$key,&$value=null){
list($key,$value)=array(array_keys($data),array_values($data));
}

//将数组连按照key=value成字符串
static function arr_str($info_arr,$connector='&'){
$feild_info=null;
if ($info_arr) {
foreach ($info_arr as $key=>$value) {
$feild_info.=$key.'='.$value.$connector;
}
}
return trim($feild_info,$connector);
}

//将数组按照键的类型（str/num）拆分成两个数组
static function sn_arr($arr,&$str_arr=null,&$num_arr=null){
foreach ($arr as $key=>$value) {
if (is_numeric($key)) {
$num[$key]=$value;
} else {
$str[$key]=$value;
}
}
list($str_arr,$num_arr)=array($str,$num);
}


//将二维数组变为一维数组
static function two_arr_one($data_arr,$son_key='id'){
$id_arr=null;
if ($data_arr) {
foreach ($data_arr as $key=>$value) {
$id_arr[$key]=$value[$son_key];
}
}
return $id_arr;
}

//输出xml字符文档
static function xml($mix_data,$root='rss',$code='utf-8'){
$xml='<?xml version="1.0" encoding="'.$code.'"?>';
$xml.='<'.$root .'>'.self::data_xml($mix_data).'</'.$root.'>';
return $xml;
}

//将数组或对象转换为xml（递归）
static function data_xml($mix_data,$num_tag='item',$mode=false){
$xml=null;
foreach ($mix_data as $key=>$val) {

if ($mode) {
$xml.=is_numeric($key) ? '<'.$num_tag.' id="'.$key.'">' : '<'.$key.'>';
} else {
$xml.=is_numeric($key) ? '<'.$num_tag.'>' : '<'.$key.'>';
}

$xml.=(is_array($val) || is_object($val)) ? self::data_xml($val) : $val;
$xml.=is_numeric($key) ? '</'.$num_tag.'>' : '</'.$key.'>';
}
return $xml;
}

//数组变ini配置数据
static function arr_ini($data){
$str='';
if ($data && is_array($data)) {
foreach ($data as $name=>$info){

if ($info && is_array($info)) {
$str.="\r\n[$name]\r\n";
foreach ($info as $key=>$value){
$str.=$key.'='.$value."\r\n";
}

}

}
}
return trim($str,"\r\n");
}

//查询fun类方法
static function tip(){
$info='<br><font color="green">';
$info.='1、字符截取：str_cut($str,$length,$from=0)<br>';
$info.='2、键值拆分：kv_arr($arr,&$key,&$value=null)<br>';
$info.='3、键值组合：arr_str($info_arr,$connector="&")<br>';
$info.='4、键类拆分：sn_arr($arr,&$str_arr=null,&$num_arr=null)<br>';
$info.='5、整合主键：two_arr_one($data_arr,$son_key="id")<br>';
$info.='6、输出XML：xml($mix_data,$root="rss",$code="utf-8")<br>';
$info.='7、转为XML：data_xml($mix_data)<br>';
$info.='8、转为INI：arr_ini($data)</font><br><br>';
return $info;
}


}