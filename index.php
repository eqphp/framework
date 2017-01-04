<?php
//xhprof_enable();
define('RUN_MODE', 'web');
$environment = get_cfg_var('environment');
empty($environment) and $environment = 'local';
define('ENVIRONMENT', $environment);
define('PATH_ROOT', realpath(dirname(__FILE__)) . '/');

include PATH_ROOT . 'class/common.php';
include PATH_ROOT . 'class/system.php';

spl_autoload_register('system::snake_case_auto_load');
register_shutdown_function('system::process_error');

//debug::flag($t1);
system::init();
list($controller, $method) = system::parse_route(false);
system::bootstrap($controller, $method);

//debug::flag($t2);
//debug::out(debug::used($t1,$t2));

//$xhprof=xhprof_disable();
//echo debug::xhprof($xhprof,'xhprof_result');