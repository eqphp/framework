<?php
//定义当前运行环境
define('RUN_MODE', 'cli');
define('ENVIRONMENT', 'test');
define('PATH_ROOT', dirname(dirname(dirname(__FILE__))). '/');

include 'library/system.php';
include 'library/shortcut.php';
spl_autoload_register('system::snake_load');

system::init();