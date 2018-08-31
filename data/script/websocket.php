<?php
namespace eqphp;

include 'cli.php';
$config = config('socket');
(new socket($config['address'], $config['secure_key']))->run();