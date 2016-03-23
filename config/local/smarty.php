<?php

return array(
	'home'=>array(
		//允许php模板、开启错误、调试
		'allow_php'=>false,
		'error'=>true,
		'debug'=>false,

		//定界符
		'left'=>'{',
		'right'=>'}',

		//模板、常量、编译存放目录
		'template'=>'view/',
		'const'=>'cache/smarty/const/',
		'compile'=>'cache/smarty/compile/',

		//缓存
		'expire'=>68400,
		'caching'=>false,
		'path'=>'cache/smarty/data/',
	),
);