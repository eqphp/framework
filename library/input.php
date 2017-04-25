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
        //trim过滤、魔术引号转义
        //if (is_string($value)) {
        //    $value = get_magic_quotes_gpc() ? trim($value) : trim(addslashes($value));
        //}
        switch ($mode) {
            //标题、关键词(去空、特殊字符、html标签)
            case 'title':
                return trim(htmlspecialchars(strip_tags($value)));
            //ID,自然数、POST的整型(0-N,ID、number)
            case 'int':
                return abs((int)($value));
            //介绍、详细内容(就留允许的html标签)
            case 'text':
                $allow_tags = '<a><img><span><b><i><em><cite><strong><br><hr>';
                $allow_tags .= '<ul><ol><li><p><h1><h2><h3><h4><h5><h6><table><tr><th><td>';
                return trim(strip_tags($value, $allow_tags));
            //bool值(0,1)
            case 'bool':
                return (bool)$value + 0;
            //数字
            case 'number':
                return secure::match($value, 'number') ? $value : 0;
            //小数、浮点数(货币、概率)
            case 'float':
                return (float)$value;
            //邮箱、用户名(注册账号时不区分大小写)
            case 'account':
                return trim(secure::symbol(strip_tags(strtolower($value))));
            //日期、时间
            case 'date':
            case 'time':
            case 'date_time':
                $option = array('date' => 'Y-m-d', 'time' => 'H:i:s', 'date_time' => 'Y-m-d H:i:s');
                $format_time = date($option[$mode], strtotime($value));
                return ($format_time === $value) ? $value : null;
            //联合复选框(checkbox)
            case 'many':
                sort($value);
                return implode(',', $value);
            //正则匹配输出
            default:
                return secure::match($value, $mode) ? $value : null;
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
            return secure::match($value, $type) ? $value : null;
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