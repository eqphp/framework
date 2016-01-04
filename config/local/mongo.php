<?php

return array(
	'server'=>'mongodb://platform:platform2014@10.10.0.214:27017/che001',
	//'server'=>'mongodb://127.0.0.1:27017',//mongodb://[username:password@]hostname[:port][/database]

	'page_size'=>20,
	
	'log'=>array(
		'is_record_exception'=>true,
		'is_record_query'=>true,
	),
	
	'exception'=>array(
		100=>"mysql can't connect !",
		101=>'database select failed',
		103=>'sql error',	
	),


);