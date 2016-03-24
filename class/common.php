<?php

//处理域名
function set_domain(){
    //获取、定义当前运行环境
    $environment = get_cfg_var('environment');
    empty($environment) and $environment = 'local';
    define('ENVIRONMENT', $environment);

    $domain = config('domain');
    $allow_host = $domain['allow_host'];
    $allow_port = $domain['allow_port'];
    list($scheme, $host, $port) = $domain['default'];
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === '1' || strtolower($_SERVER['HTTPS']) === 'on'))
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443')) {
        $scheme = 'https';
    }
    if (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], $allow_host)) {
        $host = $_SERVER['HTTP_HOST'];
    }
    if (isset($_SERVER['SERVER_PORT']) && in_array(intval($_SERVER['SERVER_PORT']), $allow_port)) {
        $port = intval($_SERVER['SERVER_PORT']);
    }

    $port = ($port === 80) ? '' : ':' . $port;
    define('SITE_DOMAIN', $host);
    define('URL_REQUEST', trim($uri, '/'));
    define('U_R_L', $scheme . '://' . $host . $port . '/');
    define('URL_URI', U_R_L . URL_REQUEST);
}

//类自动加载
function eqphp_autoload($class){
    if (isset($_SERVER['REQUEST_URI'])) {
        $root = current(explode('/', trim($_SERVER['REQUEST_URI'], '/')));
    }

    //optimize: config from memcache or redis
    $group = config('group.list');
    $path = (isset($root) && is_array($group) && in_array($root, $group)) ? $root . '/' : '';
    $module = array('a' => $path . 'action', 'm' => $path . 'model', 'p' => $path . 'plugin', 's' => 'server');

    $prefix = substr($class, 0, strpos($class, '_'));
    $dir_name = in_array($prefix, array('a', 'm', 's', 'p')) ? $module[$prefix] : 'class';
    $load_file = $dir_name . '/' . $class . '.php';

    if (strtolower($class) === 'smarty') {
        $load_file = 'data/smarty/Smarty.class.php';
    }

    if (file_exists($load_file)) {
        return include PATH_ROOT . $load_file;
    }

    //通用加载
    if (config('state.common_load') && in_array($prefix, array('a', 'm'), true)) {
        $common_option = array('a' => 'action/', 'm' => 'model/');
        $load_file = PATH_ROOT . $common_option[$prefix] . $class . '.php';
        if (file_exists($load_file)) {
            return include $load_file;
        }
    }

    //贪婪加载
    if (config('state.greedy_load')) {
        $load_file = file::search(PATH_ROOT . $dir_name, $class, $file_list, true);
        if ($load_file) {
            return include $load_file;
        }
    }

    if ($prefix === 'a') {
        logger::notice('class [' . $class . '] not found');
        http::send(404);
    }

    if ($load_file && strpos(strtolower($load_file), 'smarty_internal_') === false) {
        logger::error('class [' . $class . '] not found');
    }
}

//处理错误
function process_error(){
    $error = (object)error_get_last();
    if ($error && isset($error->type)) {
        ob_end_clean();
        $type = config($error->type, 'error');
        $log_message = $type . ' : ' . $error->message . ' [' . $error->file . ' - ' . $error->line . ']' . PHP_EOL;
        file_write(LOG_TOPIC . 'error.log', $log_message, 'a+');

        if (preg_match('/^(similar|product)$/', ENVIRONMENT)) {
            if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])){
                if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === 'xmlhttprequest') {
                    header('Content-Type:application/json; charset=utf-8');
                    exit(json_encode(array('error' => 4, 'message' => $error->message, 'data' => null)));
                }
            }
            header('location: ' . U_R_L . 'abort/error');
            exit;
        }

        header('Content-Type:text/html; charset=utf-8');
        $html = '<link rel="stylesheet" type="text/css" href="/file/static/style/basic.css">';
	$html .= '<div class="trace"><pre>';
        $html .= sprintf('<h5><b>%s</b>%s</h5>', $type, $error->message);
        $html .= sprintf('<h6>%s<b>%s</b></h6>', $error->file, $error->line);
        $html .= '</pre></div>';
        exit($html);
    }
}

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

//解析配置文件(mode:0-全局,1-分组)
function config($name, $file = 'config', $is_group = false){
    $config = 'config/';
    if (preg_match('/^(local|test|similar|product)$/', ENVIRONMENT)) {
        $config .= ENVIRONMENT . '/';
    }

    $path = $is_group ? PATH_ROOT . GROUP_NAME . '/' : PATH_ROOT;
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

//获取(restful风格)请求(URL位段-pathinfo=>get)参数值
function url($lie = 0, $type = 0){
    // $lie=$lie+n; //n为相对根目录的深度
    $param = explode('/', URL_REQUEST);
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

//解析分组配置文件
function group_config($name, $file = 'config'){
    return config($name, $file, true);
}

//调试
function out($param, $mode = 1, $is_exit = true){
    debug::out($param, $mode, $is_exit);
}

//会话
function session($key = null, $value = false){
    return session::merge($key, $value);
}

//表单值接收处理
function post($name, $mode = 'title'){
    return input::post($name, $mode);
}

//实例化数据表模型
function query($table){
    return new query($table);
}