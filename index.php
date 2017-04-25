<?php
define('RUN_MODE', 'web');
define('ENVIRONMENT', 'local');
define('PATH_ROOT', dirname(__FILE__) . '/');

include 'library/system.php';
include 'library/shortcut.php';
spl_autoload_register('system::snake_load');

//debug::flag($t1);
system::bootstrap('snake_load');
//debug::flag($t2);
//debug::out(debug::used($t1,$t2));