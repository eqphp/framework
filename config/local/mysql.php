<?php

return array(
	'server'=>array(
		array(
			'host'=>'localhost',
			'user'=>'root',
			'password'=>'',
			'database'=>'eqphp_0929',
			'port'=>3306,
			'charset'=>'utf8',
			'type'=>'master',
		),
		array(
			'host'=>'localhost',
			'user'=>'root',
			'password'=>'root',
			'database'=>'phpmyadmin',
			'port'=>3306,
			'charset'=>'utf8',
			'type'=>'slave',
		),
		array(
			'host'=>'192.168.1.102',
			'user'=>'root',
			'password'=>'',
			'database'=>'eqphp',
			'port'=>3306,
			'charset'=>'utf8',
			'type'=>'slave',
		),
	),	

	'query'=>array(
		'page_size'=>20,
	),
	
	'log'=>array(
		'is_record_exception'=>true,
		'is_record_sql'=>true,
	),

);