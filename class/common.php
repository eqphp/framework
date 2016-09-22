<?php

//获取数组key1.key2...keyN的值
function array_get($data, $map = null){
    if (is_null($map)) {
        return $data;
    }
    if (isset($data[$map])) {
        return $data[$map];
    }
    foreach (explode('.', $map) as $segment) {
        if (is_array($data) && isset($data[$segment])) {
            $data = $data[$segment];
        } else {
            return null;
        }
    }
    return $data;
}

//创建、写文件
function file_write($file_name, $string = '', $mode = 'w'){
    $open_file = fopen($file_name, $mode);
    flock($open_file, LOCK_EX);
    fwrite($open_file, $string);
    flock($open_file, LOCK_UN);
    fclose($open_file);
    return true;
}

//对象工厂
function with(){
    $param = func_get_args();
    $amount = func_num_args();
    $class = $amount ? func_get_arg(0) : '';
    switch ($amount) {
        case 0:
            return (object)array();
        case 1:
            return new $param[0];
        case 2:
            return new $class($param[1]);
        case 3:
            return new $class($param[1], $param[2]);
        case 4:
            return new $class($param[1], $param[2], $param[3]);
        case 5:
            return new $class($param[1], $param[2], $param[3], $param[4]);
        default:
            throw new Exception('param of the with function more than five', 100);
    }
}

//解析配置文件(mode:0-全局,1-分组)
function config($name, $file = 'config', $is_module = false){
    $config = 'config/';
    if (preg_match('/^(local|test|similar|product)$/', ENVIRONMENT)) {
        $config .= ENVIRONMENT . '/';
    }

    $path = $is_module ? PATH_ROOT . 'module/' . MODULE_NAME . '/' : PATH_ROOT;
    $file = $path . $config . $file . '.php';

    //环境的覆盖公共的
    if (!is_file($file)) {
        $file = str_replace('/' . ENVIRONMENT . '/', '/', $file);
        if (!is_file($file)) {
            return null;
        }
    }

    //全局变量
    $key = md5($file);
    if (empty($GLOBALS['_CONFIG'][$key])) {
        $GLOBALS['_CONFIG'][$key] = include($file);
    }
    return array_get($GLOBALS['_CONFIG'][$key], $name);
}

//获取(restful风格)请求(URL位段-pathInfo=>get)参数值
function url($lie = 0, $type = 0){
    $param = explode('/', preg_replace('/(\?.*)/', '', URL_REQUEST));
    if ($lie < count($param)) {
        $value = get_magic_quotes_gpc() ? $param[$lie] : addslashes($param[$lie]);
        if (is_int($type)) {
            return $type ? abs((int)$value) : strval(trim($value));
        }
        return regexp::match($value, $type) ? $value : null;
    }
}

//构造URL
function route(){
    $amount = func_num_args();
    $param = func_get_args();
    if ($amount < 1) {
        return U_R_L;
    }

    $uri = trim(str_replace('.', '/', $param[0]), '/');
    if ($amount === 1) {
        return U_R_L . $uri;
    }

    if ($amount === 2 && is_array($param[1])) {
        $query = str_replace('&%23=', '#', http_build_query($param[1]));
        return U_R_L . $uri . '/?' . $query;
    }

    unset($param[0]);
    $query = str_replace('/#', '#', implode('/', $param));
    return U_R_L . $uri . '/' . $query;
}

//解析分组配置文件
function module_config($name, $file = 'config'){
    return config($name, $file, true);
}

//调试
function out($param, $mode = 1, $is_exit = true){
    debug::out($param, $mode, $is_exit);
}

//模板配置
function smarty(){
    //return smarty3::tpl();
}

//会话
function session($key = null, $value = false){
    return session::merge($key, $value);
}

//多库操作获取db对象
function db($flag = 0){
    return mysql::get_instance($flag);
}

//表单值接收处理
function post($name, $mode = 'title'){
    return input::post($name, $mode);
}

//实例化数据表模型
function query($table){
    return new query($table);
}

//积分
function point($rule, $user_id, $action = ''){
    $class = 's_point_' . $rule;
    return with($class, $user_id, $action);
}