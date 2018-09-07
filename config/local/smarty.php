<?php

return array(
	'common'=>array(
		//允许php模板、开启错误、调试
		'allow_php'=>false,
		'error'=>true,
		'debug'=>false,

		//定界符
		'left'=>'{',
		'right'=>'}',

		//模板、常量、编译存放目录
		'template'=>'view/',
		'const'=>'cache/data/const/',
		'compile'=>'cache/compile/smarty/',

		//缓存
		'expire'=>68400,
		'caching'=>false,
		'path'=>'cache/data/smarty/',

		//url静态资源
		'dir_url'=>array(
			'picture'=>URL_PICTURE,
			'temp'=>URL_TEMP,
			'avatar'=>URL_AVATAR,
			'media'=>URL_MEDIA,
			'doc'=>URL_DOC,
			'html'=>URL_HTML,
			'editor'=>URL_EDITOR,
			'static'=>URL_STATIC,
			'script'=>URL_SCRIPT,
			'style'=>URL_STYLE,
			'image'=>URL_IMAGE,
		),
	),
);