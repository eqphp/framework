<?php

class lang{

    //国际化语言包解析
    static function get($name, $i18n = ''){
        if (empty($i18n)) {
            $i18n = session('i18n');
            $i18n = empty($i18n) ? 'cn' : $i18n;
        }
        $module = '';
        if (strpos($name, '::')) {
            list($module, $name) = explode('::', $name);
            $module = $module . '/';
        }
        list($file_name, $map) = explode(':', $name);
        $file = DATA_LANG . $i18n . '/' . $module . $file_name . '.php';
        $key = md5($file);
        if (empty($GLOBALS['_CONFIG'][$key])) {
            $GLOBALS['_CONFIG'][$key] = include($file);
        }
        return array_get($GLOBALS['_CONFIG'][$key], $map);
    }

    //换算值
    static function convert($value, $category = 'currency', $i18n = '', $unit = 0){

    }

    //换算单位
    static function unit($category = 'currency', $i18n = ''){

    }


}