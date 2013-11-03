<?php
// xhprof_enable(); //开启xhprof测试点
header("Cache-control:private");
//header('Content-Type:text/html; charset=utf-8');
define('dc_root',realpath(dirname(__FILE__)).'/');
$server_host=isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
define('dc_url','http://'.$server_host.'/'); //配置根URL(.htaccess)

spl_autoload_register('eqphp_autoload');
include dc_root.'server/function_lib.php'; //加载方法库

// $begin=debug::set_flag(); //开启系统debug调试点
s_system::dir(); //加载系统常量（目录/URL）
s_system::init(); //设置系统环境变量

$group=explode(',',config('group|info'));
in_array(rq(0),$group) && define('dc_group',rq(0));
$start_lie=in_array(rq(0),$group) ? 1 : 0; //配置根URL位置
$controller='a_'.rq($start_lie);
if ($controller=='a_' || !class_exists($controller)) {
$controller='a_index';
}

$method=trim(rq($start_lie+1));
if (!method_exists($controller,$method))
if (!method_exists($controller,'__call'))
if (!method_exists($controller,'__callStatic'))
if (method_exists($controller,'index')) {
$method='index';
} else {
http::send(404,0);
http::out('404 Not Found');
}

//如果控制器类是静态类(不需要实例化),需加private $static_class;
if (property_exists($controller,'static_class')) {
call_user_func(array($controller,$method),explode('/',dc_request));
} else {
call_user_func_array(array(new $controller,$method),explode('/',dc_request));
}
unset($server_host,$group,$start_lie,$controller,$method);

//类自动加载
function eqphp_autoload($lib_name) {
$server_uri=isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$group=explode(',',config('group|info'));
$rq_url=explode('/',trim($server_uri,'/'));

$dir_first=array('a'=>'action','m'=>'model');
$dir_second=array('a'=>$rq_url[0].'/action','m'=>$rq_url[0].'/model');
$dir_arr=in_array($rq_url[0],$group)?$dir_second:$dir_first;
$dir_arr=array_merge($dir_arr,array('s'=>'server','p'=>'plugin'));

$prefix=strstr($lib_name,'_',true);
$dir_name=in_array($prefix,array_keys($dir_arr))?$dir_arr[$prefix]:'class';
$execute_file=$dir_name.'/'.$lib_name.'.php';

if (strtolower($lib_name)=='smarty') {
$execute_file='data/smarty/Smarty.class.php';
}

if (file_exists($execute_file)) {
return include dc_root.$execute_file;
}

if ($prefix=='a') {
$tpl_file=dc_root.'view/'.substr($lib_name,2).'.html';
if (file_exists($tpl_file)) exit(include $tpl_file);
if ($execute_file!='action/a_.php') {
http::send(404,0);
http::out('404 Not Found');
}
}

if (!strstr(strtolower($execute_file),'smarty_internal_')){
echo "class [".$lib_name."] is error !<br>";
}

}


// $xhprof_data=xhprof_disable(); //终止xhprof测试点
// echo debug::xhprof($xhprof_data,'xhprof_all_res'); //显示xhprof测试结果
// $end=debug::set_flag(); //结束debug调试点
// debug::out(debug::used($begin,$end),1,1); //输出调试结果