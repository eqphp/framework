<?php

return array(
    'frame'=>array(
        'name'=>'eqphp',
        'version'=>'3.0',
        'size'=>'1.58MB',
    ),

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
        'list'=>array('admin','demo','api'),
        'subdomain'=>array(),
    ),

);