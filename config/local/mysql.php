<?php

return array(	
	'server'=>array(
		array(
			'host'=>'192.168.1.100',
			'user'=>'root',
			'passord'=>'',
			'database'=>'eqphp',
			'port'=>3306,
			'names'=>'utf8',
			'type'=>'master',
		),
		array(
			'host'=>'192.168.1.101',
			'user'=>'root',
			'passord'=>'',
			'database'=>'eqphp',
			'port'=>3306,
			'names'=>'utf8',
			'type'=>'slave',
		),
		array(
			'host'=>'192.168.1.102',
			'user'=>'root',
			'passord'=>'',
			'database'=>'eqphp',
			'port'=>3306,
			'names'=>'utf8',
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