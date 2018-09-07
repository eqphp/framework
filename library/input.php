<?php

//rely on: secure
class input{

    //GET传值
    static function get($name, $mode = 'get'){
        if ($mode === 'isset') {
            return isset($_GET[$name]);
        }
        if (isset($_GET[$name])) {
            return self::process($_GET[$name], $mode);
        }
    }

    //POST传值
    static function post($name, $mode = 'post'){
        if ($mode === 'isset') {
            return isset($_POST[$name]);
        }
        if (isset($_POST[$name])) {
            return self::process($_POST[$name], $mode);
        }
    }

    //REQUEST传值
    static function request($name, $mode = 'request'){
        if ($mode === 'isset') {
            return isset($_REQUEST[$name]);
        }
        if (isset($_REQUEST[$name])) {
            return self::process($_REQUEST[$name], $mode);
        }
    }

    //SERVER传值
    static function server($name, $mode = 'server'){
        $name = strtoupper($name);
        if ($mode === 'isset') {
            return isset($_SERVER[$name]);
        }
        if (isset($_SERVER[$name])) {
            return self::process($_SERVER[$name], $mode);
        }
    }

    //COOKIE传值
    static function cookie($name, $mode = 'cookie'){
        if ($mode === 'isset') {
            return isset($_COOKIE[$name]);
        }
        if (isset($_COOKIE[$name])) {
            return self::process($_COOKIE[$name], $mode);
        }
    }

    //对输入值处理
    private static function process($value, $mode){
        //原味输出
        $input_type = array('get', 'post', 'request', 'server','cookie');
        if (in_array($mode, $input_type, true)) {
            return $value;
        }
        if (is_string($value) && !get_magic_quotes_gpc()) {
            $value = addslashes($value);
        }
        switch ($mode) {
            //标题、关键词(去空、转html实体)
            case 'title':
                return trim(htmlspecialchars($value, ENT_NOQUOTES));
            //介绍、详细内容(过滤不允许的html标签)
            case 'text':
                $allow_tags = '<a><img><span><b><i><em><cite><strong><br><hr>';
                $allow_tags .= '<ul><ol><li><p><h1><h2><h3><h4><h5><h6><table><tr><th><td>';
                return trim(strip_tags($value, $allow_tags));
            //ID,自然数、POST的整型(0-N,ID、number)
            case 'int':
                return abs((int)($value));
            //bool值(0,1)
            case 'bool':
                return (bool)$value + 0;
            //枚举值
            case 'enum':
                sort($value);
                return implode(',',$value);
            //数字(整数、小数、浮点数)
            case 'number':
                return $value + 0;
            //日期、时间
            case 'date':
            case 'time':
            case 'date_time':
                $option = array('date' => 'Y-m-d', 'time' => 'H:i:s', 'date_time' => 'Y-m-d H:i:s');
                $format_time = date($option[$mode], strtotime($value));
                return ($format_time === $value) ? $format_time : '';
            //正则匹配输出
            default:
                return util::match($value, $mode) ? $value : '';
        }
    }

    //批量获取原值，用于校验
    static function fetch($list, $type = 'post'){
        if (is_string($list) && strpos($list,',') !== false) {
            $list=explode(',',$list);
        }
        $buffer=array();
        if (is_array($list)) {
            foreach ($list as $key) {
                $buffer[$key] = self::$type($key, $type);
            }
        }
        return $buffer;
    }

    //获取(restful风格)请求(URL位段-pathInfo=>get)参数值
    static function url($lie = 0, $type = 0){
        $param = explode('/', preg_replace('/(\?.*)/', '', URL_REQUEST));
        if ($lie < count($param)) {
            $value = get_magic_quotes_gpc() ? $param[$lie] : addslashes($param[$lie]);
            if (is_int($type)) {
                return $type ? abs((int)$value) : strval(trim($value));
            }
            return util::match($value, $type) ? $value : null;
        }
    }

    //批量安全过滤、接收数据
    static function filter($data, $type = 'post', $map = array()){
        if (is_array($data)) {
            foreach ($data as $name => $rule) {
                $buffer[$name] = self::$type($name, $rule);
            }
            if (is_array($map) && $map && isset($buffer)) {
                foreach ($map as $key => $value) {
                    if (isset($buffer[$key])) {
                        $buffer[$value] = $buffer[$key];
                        unset($buffer[$key]);
                    }
                }
            }
        }
        return isset($buffer) ? $buffer : array();
    }


}