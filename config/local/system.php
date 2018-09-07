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
        'default'=>array('http','127.0.0.1',''),
        'allow_host'=>array('www.eqphp.com','localhost','127.0.0.1','192.168.1.1'),
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