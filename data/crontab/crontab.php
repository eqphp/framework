<?php
//定义当前运行环境
define('RUN_MODE', 'cli');
define('ENVIRONMENT', 'local');
define('PATH_ROOT', realpath(substr(dirname(__FILE__), 0, -12)) . '/');

include 'library/system.php';
include 'library/shortcut.php';
spl_autoload_register('system::snake_load');
register_shutdown_function('system::process_error');

system::init();

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