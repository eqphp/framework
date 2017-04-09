<?php
//xhprof_enable();
define('RUN_MODE', 'web');
define('ENVIRONMENT', 'local');
define('PATH_ROOT', realpath(dirname(__FILE__)) . '/');

include 'library/system.php';
include 'library/shortcut.php';
spl_autoload_register('system::snake_load');
register_shutdown_function('system::process_error');

//debug::flag($t1);

system::bootstrap();
//debug::flag($t2);
//debug::out(debug::used($t1,$t2));

//$xhprof=xhprof_disable();
//echo debug::xhprof($xhprof,'xhprof_result');