<?php
class p_fun{

static function upload($params,$tpl){
$data['type']=isset($params['type']) ? $params['type'] : 'pic';
$data['dir']=isset($params['dir']) ? $params['dir'] : 16;
$data['id']=isset($params['id']) ? $params['id'] : 0;
$data['width']=isset($params['width']) ? $params['width'] : 320;
$data['height']=isset($params['height']) ? $params['height'] : 25;
return tpl::show('plugin/iframe_upload',$data,$tpl);
}

//输出指定数目的对象
static function repeat($params,$tpl){
$obj=isset($params['obj'])?$params['obj']:'<br>';
$num=isset($params['num'])?$params['num']:0;
return str_repeat($obj,$num);
}

//输出swf格式动画
static function flash($params,$tpl){
$data['src']=isset($params['src']) ? $params['src'] : '';
$data['width']=isset($params['width']) ? $params['width'] : 400;
$data['height']=isset($params['height']) ? $params['height'] : 300;
$data['title']=isset($params['title']) ? $params['title'] : 'EQPHP flash';
return tpl::show('plugin/flash',$data,$tpl);
}

//输出flv格式视频
static function flv($params,$tpl){
$data['src']=isset($params['src']) ? $params['src'] : '';
$data['width']=isset($params['width']) ? $params['width'] : 600;
$data['height']=isset($params['height']) ? $params['height'] : 450;

$data['autostart']=$data['allowfullscreen']='false';
isset($params['autostart']) && $data['autostart']=$params['autostart'];
isset($params['allowfullscreen']) && $data['allowfullscreen']=$params['allowfullscreen'];
return tpl::show('plugin/flv',$data,$tpl);
}


/******************************
//++++项目实际使用function++++//
*******************************/

}