<?php

class p_function{

    //页头
    static function head($params, $tpl){
        extract($params);
        $script = isset($script) ? explode('|', $script) : array();
        $group_script = isset($group_script) ? explode('|', $group_script) : array();
        $style = isset($style) ? explode('|', $style) : array();
        $group_style = isset($group_style) ? explode('|', $group_style) : array();
        $data = compact('script', 'group_script', 'style', 'group_style');
        return s_smarty::show('plugin/head', $data, $tpl);
    }

    //输出swf格式动画
    static function flash($params, $tpl){
        extract($params);
        $src = isset($src) ? $src : '';
        $width = isset($width) ? $width : 400;
        $height = isset($height) ? $height : 300;
        $title = isset($title) ? $title : 'EQPHP flash player';
        $data = compact('src', 'width', 'height', 'title');
        return s_smarty::show('plugin/flash', $data, $tpl);
    }

    //输出flv格式视频
    static function media($params, $tpl){
        extract($params);
        $src = isset($src) ? $src : '';
        $width = isset($width) ? $width : 600;
        $height = isset($height) ? $height : 450;
        $autostart = isset($autostart) ? $autostart : 'true';
        $allowfullscreen = isset($allowfullscreen) ? $allowfullscreen : 'true';
        $data = compact('src', 'width', 'height', 'autostart', 'allowfullscreen');
        return s_smarty::show('plugin/media', $data, $tpl);
    }


}