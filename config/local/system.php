<?php

return array(
    'state'=>array(
        'environment'=>'local',//(local,test,similar,product)
        'timezone'=>'Asia/Chongqing',
        'error_switch'=>true,

        'common_load'=>true,
        'greedy_load'=>true,
        'config_route'=>true,
        'auto_login'=>true,
    ),
	
    'domain'=>array(
        'cookie'=>'127.0.0.1',
        'allow_port'=>array(80,8080),
        'default'=>array('http','127.0.0.1',80),
        'allow_host'=>array('127.0.0.1'),
    ),

    'module'=>array(
        'list'=>array('admin','demo'),
        'subdomain'=>array(),
    ),

    'secure'=>array(
        'key'=>'EQPHP',
        'csrf_name'=>'eqphp_csrf_token',
        'pad'=>'c22ae177a0dd4c4db335a4503870a1d1',
    ),

);