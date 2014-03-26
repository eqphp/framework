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
    return (count($arr) == 1) ? $info[$arr[0]] : $info[$arr[0]][$arr[1]];
}

//解析分组配置文件
function group_config($name,$file='config'){
    $dir=dc_group_dir.'config/';
    $dir.=strpos($name,'.php') ? 'php_data/' : $file.'.ini';
    return config($name,$dir,true);
}

//获取(restful风格)请求(URL位段-pathinfo=>get)参数值
function url($lie=0,$type=0){
    // $lie=$lie+n; //n为相对根目录的深度
    $server_uri=isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $param=explode('/',trim($server_uri,'/'));
    if ($lie < count($param)) {
        $value=get_magic_quotes_gpc() ? $param[$lie] : addslashes($param[$lie]);
        if (is_int($type)) return $type ? abs((int)$value) : strval(trim($value));
        return safe::reg($value,$type) ? $value : null;
    }
}

//构造sql语句
function sql($option,$from='',$where=null){
    if (is_array($option)) {
        $sql='';
        $key_arr=array('select','from','where','group','having','order','limit');
        foreach ($key_arr as $key) {
            $value=($key == 'group' || $key == 'order') ? $key.' by' : $key;
            $sql.=(isset($option[$key]) && trim($option[$key])) ? ' '.$value.' '.trim($option[$key]) : '';
        }
        return $sql;
    }

    $sql='select '.$option.' from '.$from;
    if ($where !== null) {
        $sql.=' where '.(preg_match('/^[0-9]*[1-9][0-9]*$/',$where) ? 'id='.$where : $where);
    }
    return $sql;
}


/*==================*/
/*****类方法简写*****/
/*==================*/

//调试方法
function out($dim='please input $dim !',$mode=1,$stop=false){
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
// function mc(){
// return s_memcache::many();
// }

//表单值接收处理方法
function post($name,$mode='title'){
    return form::post($name,$mode);
}

function query($table){
    return new query($table);
}