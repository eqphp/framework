<?php

return array(

	'system'=>array(
		'picture'=>array('gif','jpg','jpeg','png','bmp'),
		'media'=>array('swf','flv','mp3','mp4','wav','wmv','mid','avi','mpeg','rm','rmvb','mov','m4v','3gp'),
		'document'=>array('txt','doc','docx','pdf','xls','xlsx','ppt'),
		'html'=>array('html','htm'),
		'zip'=>array('zip','rar','gz'),
	),
	'editor'=>array(
		'editor/picture'=>array('gif','jpg','jpeg','png','bmp'),
		'editor/media'=>array('swf','flv','mp3','mp4','mid','mov','m4v','3gp'),
		'editor/file'=>array('doc','xls','ppt','zip','rar'),
	),

	'error'=>array(
		0=>'上传成功',
		1=>'选择上传文件失败',
		2=>'上传的文件体积超限（php环境配置）',
		3=>'上传的文件体积超限(表单限制)',
		4=>'上传未完成，仅有部分文件上传',
		5=>'没有文件被上传，上传失败',
		6=>'请选择上传文件',
	 	7=>'没有文件被上传，上传失败',
		8=>'上传的文件体积超限（php程序项目限制）',
		9=>'伪冒的文件头信息',
		10=>'不允许的文件格式',
		11=>'上传目录未创建',
		12=>'上传目录不可写入文件',
	 	13=>'文件名含有特殊字符',
	 	14=>'移动文件到指定目录失败',
		15=>'上传图片宽度小于规格值',
		16=>'上传图片宽度大于规格值',
		17=>'上传图片高度小于规格值',
		18=>'上传图片高度大于规格值',
	),
);
