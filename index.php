<?php
//web,cli
define('RUN_MODE', 'web');
//local,test,similar,product
define('ENVIRONMENT', 'local');
define('PATH_ROOT', dirname(__FILE__) . '/');

//snake,camel
include 'config/' . ENVIRONMENT . '/shortcut.php';
include 'library/system.php';
spl_autoload_register('system::snake_load');
system::bootstrap('snake_load');

//composer
//include 'vendor/autoload.php';
//eqphp\system::bootstrap('composer');