<?php

return array(
    'frame'=>array(
        'name'=>'eqphp',
        'version'=>'3.0',
        'size'=>'2.11MB',
    ),

    'state'=>array(
        'environment'=>'development',//(local|test|similar|product)
        'error_tip'=>true,
        'common_load'=>true,
        'greedy_load'=>true,
        'config_route'=>true,
        'auto_login'=>true,
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