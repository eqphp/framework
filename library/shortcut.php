<?php

//对象工厂
function with(){
    return call_user_func_array('basic::with', func_get_args());
}

//调试
function out($param, $mode = 1, $is_exit = true){
    debug::out($param, $mode, $is_exit);
}

//解析分组配置文件
function config($name, $is_module = false){
    return system::config($name, $is_module);
}



//模板配置
function smarty(){
    return smarty3::tpl();
}

//会话
function session($key = null, $value = false){
    return session::merge($key, $value);
}

//多库操作获取db对象
function db($flag = 0){
    return mysql::get_instance($flag);
}

//获取url参数
function url($lie, $type = 0){
    return input::url($lie, $type);
}

//表单值接收处理
function post($name, $mode = 'title'){
    return input::post($name, $mode);
}

//实例化数据表模型
function query($table){
    return new query($table);
}