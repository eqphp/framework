<?php

return array(
	'service'=>array(
		'echo'			=>	'http://10.10.0.202:9010',
		'user'			=>	'http://172.16.1.37:80',
		'auth'			=>	'http://172.16.1.38:80',
		'search'		=>	'http://172.16.1.42:80',
		'company'		=>	'http://172.16.1.40:80',
		'banking'		=>	'http://10.10.0.203:9005',
		'support'		=>	'http://10.10.0.202:9006',
		'transaction'	=>	'http://10.10.0.202:9007',
		'backend'		=>	'http://172.16.1.111:80',
	),
	'api'=>array(
		'login'=>'auth.login',
		'register'=>'user.register',
		'user'=>'user.user',
		'credit'=>'banking.updateamount',
		'exist'=>'echo.checkOnly',
	 	'payment'=>'transaction.transaction.init',
	 	'back_card'=>'banking.bankaccount',
		'message'=>'support.message',
	
	),	
);