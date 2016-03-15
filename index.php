<?php
//xhprof_enable();
define('PATH_ROOT', realpath(dirname(__FILE__)) . '/');
include PATH_ROOT . 'class/common.php';

set_domain();
SITE_DOMAIN or exit('not allowed host');
spl_autoload_register('eqphp_autoload');
register_shutdown_function('process_error');

include PATH_ROOT . 'class/system.php';

system::init();
debug::set_debug_constant();

//debug::flag($t1);
list($controller, $method) = system::parse_route();
system::bootstrap($controller, $method);

//debug::flag($t2);
//debug::out(debug::used($t1,$t2));

//$xhprof=xhprof_disable();
//echo debug::xhprof($xhprof,'xhprof_result');