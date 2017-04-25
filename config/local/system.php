<?php

return array(
    'state'=>array(
        'timezone'=>'Asia/Chongqing',
        'error_switch'=>true,

        'greedy_load'=>true,
        'config_route'=>true,
        'auto_login'=>true,
    ),
	
    'domain'=>array(
        'cookie'=>'',
        'default'=>array('http','eqphp.oschina.mopaasapp.com',''),
        'allow_host'=>array('eqphp.oschina.mopaasapp.com','127.0.0.1','localhost'),
    ),

    'module'=>array(
        'list'=>array('admin','demo'),
        'subdomain'=>array(),
    ),

    'secure'=>array(
        'key'=>'b335a4503870a1d1',
        'csrf_name'=>'csrf_token',
        'pad'=>'c22ae177a0dd4c4db335a4503870a1d1',
    ),

);