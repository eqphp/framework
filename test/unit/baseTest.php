<?php

//定义当前运行环境
define('RUN_MODE', 'cli');
$environment = get_cfg_var('environment');
empty($environment) and $environment = 'local';
define('ENVIRONMENT', $environment);
define('PATH_ROOT', realpath(substr(dirname(__FILE__), 0, -9)) . '/');

include PATH_ROOT . 'class/common.php';
include PATH_ROOT . 'class/system.php';

spl_autoload_register('system::snake_case_auto_load');
register_shutdown_function('system::process_error');

system::init();