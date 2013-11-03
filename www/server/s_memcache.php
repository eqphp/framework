<?php

class s_memcache{


//memcache缓存初始化
static function one(){
static $mem;
if (!isset($mem) || !($mem instanceof Memcache)) {
$mem=new Memcache;
}
$config_arr=config('memcache','memcache');
$mem->connect($config_arr['host'],$config_arr['port']);
return $mem;
}

//memcache集群初始化（多服务器mc集群是请删掉ini配置里的第一项）
static function many(){
static $mem;
if (isset($mem) && ($mem instanceof Memcache)) {
return $mem;
}

$mem=new Memcache;
$mc_list=config(null,'memcache');
foreach ($mc_list as $key=>$mc) {
$mem->addServer($mc['host'],$mc['port'],true,$mc['weight'],1,15,true,array('log','memcache'));
}
return $mem;
}








}