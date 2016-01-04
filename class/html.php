<?php

class html{

    //定义魔术方法
    static function __callStatic($name,$param){
        $attribute='';
        if (isset($param[1]) && $param[1]) {
            $attribute=' '.http_build_query($param[1],'',' ');
        }
        return "<{$name}{$attribute}>".$param[0]."</{$name}>";
    }

    //创建有序列表
    static function ul($data,$attribute=''){
        return self::_list($data,$attribute,'ul');
    }

    //创建无序列表
    static function ol($data,$attribute=''){
        return self::_list($data,$attribute,'ol');
    }

    private static function _list($data,$attribute=array(),$tags='ul'){
        $attribute=self::_set_attribute($attribute);
        $html="<{$tags}{$attribute}>".PHP_EOL;
        foreach ($data as $value) {
            $html.="<li>{$value}</li>".PHP_EOL;
        }
        return $html."</{$tags}>";
    }

    // 将属性转化为字符串
    static function _set_attribute($attribute){
        $html='';
        if ($attribute && is_array($attribute)) {
            foreach ($attribute as $key=>$value) {
                $html.=' '.$key.'="'.$value.'"';
            }
        }
        return $html;
    }

    //创建自定义列表
    static function dl($data,$attribute=array()){
        $attribute=self::_set_attribute($attribute);
        $html="<dl{$attribute}>".PHP_EOL;
        $html.="<dt>{$data[0]}</dt>".PHP_EOL;
        unset($data[0]);
        foreach ($data as $value) {
            $html.="<dd>{$value}</dd>".PHP_EOL;
        }
        return $html.'</dl>';
    }

    //创建图像
    static function image($src,$attribute=array()){
        $attribute=self::_set_attribute($attribute);
        return '<img src="'.$src.'"'.$attribute.'>';
    }

    //创建表格
    static function table($data,$attribute=array()){
        $attribute=self::_set_attribute($attribute);
        $html="<table{$attribute}>".PHP_EOL;
        if (isset($data['title']) && $data['title']) {
            $html.='<tr><th>'.implode('</th><th>',$data['title']).'</th></tr>'.PHP_EOL;
            unset($data['title']);
        }

        if (isset($data['value']) && $data['value']) {
            foreach ($data['value'] as $value) {
                $html.='<tr><td>'.implode('</td><td>',$value).'</td></tr>'.PHP_EOL;
                unset($data['value']);
            }
        }
        return $html.'<table>';
    }

    //创建脚本标签
    static function script($data,$type='path'){
        switch($type){
        case 'path':
            return '<script type="text/javascript" src="'.$data.'"></script>';
        case 'back_refresh':
            $refer=isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : U_R_L;
            header('Location: '.$refer);
            exit;
        case 'alert':
            $script='alert("'.$data.'");';
            break;
        case 'alert_back':
            $script='alert("'.$data.'");location.href="javascript:history.go(-1)";';
            break;
        case 'alert_skip':
            $script='alert("'.$data[0].'");location.href="'.$data[1].'";';
            break;
        case 'skip':
            $script='location.href="'.$data.'";';
            break;
        case 'script':
        default :
            $script=$data;
        }
        return '<script type="text/javascript">'.$script.'</script>';
    }

    //创建样式链接标签
    static function link($href,$type='text/css',$rel='stylesheet'){
        return '<link rel="'.$rel.'" type="'.$type.'" href="'.$href.'" />';
    }

    //创建meta标签
    static function meta($attribute,$content,$is_name=true){
        $attribute=($is_name ? 'name=' : 'http-equiv=').'"'.$attribute.'"';
        return '<meta '.$attribute.' Content="'.$content.'" />';
    }

}