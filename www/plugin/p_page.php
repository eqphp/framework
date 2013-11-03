<?php

class p_page{

//页头
static function head($params,$tpl){

$data['script']=$data['group_script']=$data['style']=$data['group_style']=null;
isset($params['script']) && $data['script']=explode('|',$params['script']);
isset($params['group_script']) && $data['group_script']=explode('|',$params['group_script']);
isset($params['style']) && $data['style']=explode('|',$params['style']);
isset($params['group_style']) && $data['group_style']=explode('|',$params['group_style']);

return tpl::show('plugin/head',$data,$tpl);
}






}