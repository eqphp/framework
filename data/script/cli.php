<?php
//定义当前运行环境
define('RUN_MODE', 'cli');
define('ENVIRONMENT', 'local');
define('PATH_ROOT', dirname(dirname(dirname(__FILE__))) . '/');

include PATH_ROOT . 'vendor/autoload.php';
eqphp\system::init();

//处理GET参数
if (isset($argv[1])) {
    $argv[1] = str_replace('?', '', $argv[1]);
    parse_str($argv[1], $_GET);
}

//动态调用类控制器
if (isset($_GET['action'])) {
    if (strpos($_GET['action'], '::')) {
        call_user_func($_GET['action']);
    } elseif (strpos($_GET['action'], '/')) {
        list($controller, $method) = explode('/', $_GET['action']);
        call_user_func_array(array(new $controller, $method), array());
    }
}