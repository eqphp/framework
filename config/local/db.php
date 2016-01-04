<?php

return array(
	'server'=>array(
		'host'=>'localhost',
		'port'=>3306,
		'user'=>'root',
		'password'=>'',
		'database'=>'eqphp_0929',
		'names'=>'utf8',
	),

	'query'=>array(
		'page_size'=>20,
	),
	
	'log'=>array(
		'is_record_exception'=>true,
		'is_record_sql'=>true,
	),
	
	'exception'=>array(
		100=>"mysql can't connect !",
		101=>'database select failed',
		103=>'sql error',	
	),
);