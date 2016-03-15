<?php

class basic{

    //输出时过滤html标签、反转义字符
    static function put($string, $allow_tags = null){
        if (!get_magic_quotes_gpc()) {
            $string = stripslashes($string);
        }
        if (is_null($allow_tags)) {
            $allow_tags = config('allow_tags', 'secure');
        }
        return strip_tags($string, $allow_tags);
    }

    //字符截取
    static function cut($string, $length = 15, $tail = '...'){
        if (mb_strlen($string) <= $length) {
            return $string;
        }
        return mb_substr($string, 0, $length, 'UTF-8') . $tail;
    }

    //获取指定长度的随机字符串
    static function code($len = 4, $mode = 1){
        $data = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= $data[rand(0, 55)];
        }
        $option = array($code, strtolower($code), strtoupper($code));
        return $option[$mode];
    }

    //转换为指定精度倍数的整数
    static function int($number, $precision = 0, $is_round = false){
        $precision = (int)$precision;
        $is_round and $number = round($number, $precision);
        $number *= pow(10, $precision);
        return (int)$number;
    }

    //转换为指定精度位数的浮点数
    static function float($number, $precision = 2, $is_round = false){
        if ($is_round) {
            return round($number, $precision);
        }
        $by = pow(10, (int)$precision);
        $number = (int)($number * $by);
        return $number / $by;
    }

    //毫秒级时间戳
    static function microtime(){
        list($usec, $sec) = explode(' ', microtime());
        $microtime = (float)$usec + (float)$sec;
        return round($microtime * 1000);
    }

    //生成36位uuid
    static function uuid(){
        $string = md5(uniqid(mt_rand(), true));
        $middle = chunk_split(substr($string, 4, 16), 4, chr(45));
        return substr($string, 0, 4) . $middle . substr($string, -12);
    }

    //记录集排序取列
    static function array_field_sort($data, $column = 'id'){
        $buffer = array();
        if ($data && is_array($data)) {
            //[field:[k1:v1,k2:v2, ... kn:vn], ... KN]
            if (strpos($column, ',')) {
                $field_list = explode(',', $column);
                foreach ($data as $key => $value) {
                    foreach ($field_list as $field) {
                        $buffer[$field][$key] = $value[$field];
                    }
                }
                return $buffer;
            }
            //[k1:v1,k2:v2, ... kn:vn]
            foreach ($data as $key => $value) {
                $buffer[$key] = $value[$column];
            }
        }
        return $buffer;
    }

    //记录集指定列集合
    static function array_field($data, $column = 'id', $is_string = false){
        $buffer = array();
        if ($data && is_array($data)) {
            //[field:[k1,k2, ... kn], ... KN]
            if (strpos($column, ',')) {
                $field_list = explode(',', $column);
                foreach ($data as $value) {
                    foreach ($field_list as $field) {
                        $buffer[$field][] = $value[$field];
                    }
                }
                return $buffer;
            }
            //[k1,k2, ... kn] || k1,k2, ... kn
            foreach ($data as $value) {
                array_push($buffer, $value[$column]);
            }
            $buffer = array_unique($buffer);
            $is_string and $buffer = implode(',', $buffer);
        }
        return $buffer;
    }

    //处理记录集(php5.5内置)
    static function array_column($data, $column, $key = 'id'){
        $buffer = array();
        if ($data && is_array($data)) {
            //[key:value, ... N]
            if (strpos($column, ',') === false) {
                foreach ($data as $value) {
                    $buffer[$value[$key]] = $value[$column];
                }
                return $buffer;
            }
            //[key:[k:v, ... n], ... N]
            $field_list = explode(',', $column);
            foreach ($data as $value) {
                $id = $value[$key];
                foreach ($field_list as $field) {
                    $buffer[$id][$field] = $value[$field];
                }
            }
        }
        return $buffer;
    }

    //数组添加指定map的键值
    static function array_set(&$data, $map, $value){
        if (is_null($map)) {
            return $data = $value;
        }
        $maps = explode('.', $map);
        while (count($maps) > 1) {
            $map = array_shift($maps);
            if (!isset($data[$map]) or !is_array($data[$map])) {
                $data[$map] = array();
            }
            $data =& $data[$map];
        }
        $data[array_shift($maps)] = $value;
        return $data;
    }

    //数组卸载指定的map
    static function array_unset(&$data, $map){
        $maps = explode('.', $map);
        while (count($maps) > 1) {
            $map = array_shift($maps);
            if (!isset($data[$map]) or !is_array($data[$map])) {
                return;
            }
            $data =& $data[$map];
        }
        unset($data[array_shift($maps)]);
    }

    //无限分类递归
    static function tree($data, $parent_id = 0, $level = 0){
        static $tree = array();
        foreach ($data as $key => $value) {
            if ($value['parent_id'] == $parent_id) {
                $value['level'] = $level;
                $tree[] = $value;
                unset($data[$key]);
                self::tree($data, $value['id'], $level + 1);
            }
        }
        return $tree;
    }

}