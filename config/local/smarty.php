<?php

return array(
	'home'=>array(
		//����phpģ�塢�������󡢵���
		'allow_php'=>false,
		'error'=>true,
		'debug'=>false,

		//�����
		'left'=>'{',
		'right'=>'}',

		//ģ�塢������������Ŀ¼
		'template'=>'view/',
		'const'=>'cache/smarty/const/',
		'compile'=>'cache/smarty/compile/',

		//����
		'expire'=>68400,
		'caching'=>false,
		'path'=>'cache/smarty/data/',
	),
);