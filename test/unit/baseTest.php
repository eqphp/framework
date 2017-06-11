<?php
//定义当前运行环境
define('RUN_MODE', 'cli');
define('ENVIRONMENT', 'test');
define('PATH_ROOT', dirname(dirname(dirname(__FILE__))). '/');

//snake,camel
include PATH_ROOT . 'config/' . ENVIRONMENT . '/shortcut.php';
include PATH_ROOT . 'library/system.php';
spl_autoload_register('system::snake_load');
system::init();

//composer
//include PATH_ROOT . 'vendor/autoload.php';
//eqphp\system::init();