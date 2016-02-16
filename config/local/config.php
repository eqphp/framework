<?php

return array(
    'frame'=>array(
        'name'=>'eqphp',
        'version'=>'3.0',
        'size'=>'2.11MB',
    ),

    'state'=>array(
        'environment'=>'local',//(local,test,similar,product)
        'error_switch'=>true,
        'common_load'=>true,
        'greedy_load'=>true,
        'config_route'=>true,
        'auto_login'=>true,
    ),
	
    'domain'=>array(
        'allow_host'=>array('eqphp.com','www.eqphp.com'),
        'allow_port'=>array(80,8080),
        'default'=>array('http','www.eqphp.com',80),
    ),

    'group'=>array(//'news','fun','ask','work','market',
        'list'=>array('common','admin','user','blog'),
        'subdomain'=>false,
    ),

    'mobile_api'=>array(
        'category'=>array('android','ios'),
        'android_key'=>'#*!@android%$&',
        'ios_key'=>'#*!@ios%$&',
    ),

);