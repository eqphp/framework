<?php

class html{

    //定义魔术方法
    static function __callStatic($name, $param){
        $attribute = '';
        if (isset($param[1]) && $param[1]) {
            $attribute = self::_set_attribute($param[1]);
        }
        return "<{$name}{$attribute}>" . $param[0] . "</{$name}>";
    }

    //创建有序列表
    static function ul($data, $attribute = ''){
        return self::_list($data, $attribute, 'ul');
    }

    //创建无序列表
    static function ol($data, $attribute = ''){
        return self::_list($data, $attribute, 'ol');
    }

    private static function _list($data, $attribute = array(), $tags = 'ul'){
        $attribute = self::_set_attribute($attribute);
        $html = "<{$tags}{$attribute}>" . PHP_EOL;
        foreach ($data as $value) {
            $html .= "<li>{$value}</li>" . PHP_EOL;
        }
        return $html . "</{$tags}>";
    }

    // 将属性转化为字符串
    static function _set_attribute($attribute){
        $html = '';
        if ($attribute && is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $html .= ' ' . $key . '="' . $value . '"';
            }
        }
        return $html;
    }

    //创建自定义列表
    static function dl($data, $attribute = array()){
        $attribute = self::_set_attribute($attribute);
        $html = "<dl{$attribute}>" . PHP_EOL;
        $html .= "<dt>{$data[0]}</dt>" . PHP_EOL;
        unset($data[0]);
        foreach ($data as $value) {
            $html .= "<dd>{$value}</dd>" . PHP_EOL;
        }
        return $html . '</dl>';
    }

    //创建图像
    static function image($src, $attribute = array()){
        $attribute = self::_set_attribute($attribute);
        return '<img src="' . $src . '"' . $attribute . '>';
    }

    //创建表格
    static function table($data, $attribute = array()){
        $attribute = self::_set_attribute($attribute);
        $html = "<table{$attribute}>" . PHP_EOL;
        if (isset($data[0]) && $data[0]) {
            $html .= '<tr><th>' . implode('</th><th>', $data[0]) . '</th></tr>' . PHP_EOL;
        }

        if (isset($data[1]) && $data[1]) {
            foreach ($data[1] as $value) {
                $html .= '<tr><td>' . implode('</td><td>', array_values($value)) . '</td></tr>' . PHP_EOL;
            }
        }
        return $html . '</table>';
    }

    //创建脚本标签
    static function script($data, $type = 'path'){
        switch ($type) {
            case 'path':
                return '<script type="text/javascript" src="' . $data . '"></script>';
            case 'back_refresh':
                //Notice secure refer
                $refer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : U_R_L;
                header('Location: ' . $refer);
                break;
            case 'alert':
                $script = 'alert("' . $data . '");';
                break;
            case 'alert_back':
                $script = 'alert("' . $data . '");location.href="javascript:history.go(-1)";';
                break;
            case 'alert_skip':
                $script = 'alert("' . $data[0] . '");location.href="' . $data[1] . '";';
                break;
            case 'skip':
                $script = 'location.href="' . $data . '";';
                break;
            case 'script':
            default :
                $script = $data;
        }
        return '<script type="text/javascript">' . $script . '</script>';
    }

    //创建样式链接标签
    static function link($href, $type = 'text/css', $rel = 'stylesheet'){
        return '<link rel="' . $rel . '" type="' . $type . '" href="' . $href . '" />';
    }

    //创建meta标签
    static function meta($content, $attribute, $is_name = true){
        $attribute = ($is_name ? 'name=' : 'http-equiv=') . '"' . $attribute . '"';
        return '<meta ' . $attribute . ' Content="' . $content . '" />';
    }

    //返回分页导航,$page_num取值为2至10(备选)
    static function mark($url, $page_count, $page = 1, $show_num = 3){
        $mark = '';
        if ($page_count < 2) {
            return $mark;
        }

        if ($page_count < 11) {
            for ($i = 1; $i <= $page_count; $i++) {
                if ($i == $page) {
                    $mark .= '<a class="current_page">' . $i . '</a>';
                } else {
                    $mark .= '<a href="' . $url . $i . '">' . $i . '</a>';
                }
            }
            return $mark;
        }

        if ($page > $show_num + 1) {
            $mark .= '<a href="' . $url . '1">1</a>';
        }
        if ($page > $show_num + 2) {
            $mark .= '<a href="' . $url . ($page - 1) . '">Last</a>';
        }

        $min = $page - $show_num;
        if ($min < 1) {
            $min = 1;
        }
        $max = $page + $show_num;
        if ($max > $page_count) {
            $max = $page_count;
        }

        for ($i = $min; $i <= $max; $i++) {
            if ($i == $page) {
                $mark .= '<a class="current_page">' . $i . '</a>';
            } else {
                $mark .= '<a href="' . $url . $i . '">' . $i . '</a>';
            }
        }

        if ($page < $page_count - $show_num - 1) {
            $mark .= '<a href="' . $url . ($page + 1) . '">Next</a>';
        }
        if ($page < $page_count - $show_num) {
            $mark .= '<a href="' . $url . $page_count . '">' . $page_count . '</a>';
        }

        $mark .= '<input class="skip_input" type="text" url="' . $url . '" maxlength="4" size="5">';
        return $mark;
    }

}