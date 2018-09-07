<?php

class util{

    //获取元数据包
    static function meta($name){
        $name = explode('.', $name);
        $file = DATA_META . array_shift($name) . '.php';
        $key = md5($file);
        if (empty($GLOBALS['_META'][$key])) {
            $GLOBALS['_META'][$key] = include($file);
        }
        return self::array_get($GLOBALS['_META'][$key], $name);
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

    //生成36位uuid
    static function uuid(){
        $string = md5(uniqid(mt_rand(), true));
        $middle = chunk_split(substr($string, 4, 16), 4, chr(45));
        return substr($string, 0, 4) . $middle . substr($string, -12);
    }

    //对象工厂
    static function with(){
        $param = func_get_args();
        if (isset($param[0])) {
            if (is_object($param[0])) {
                return $param[0];
            }
            $hash = md5(json_encode($param));
            if (isset($GLOBALS['_OBJECT'][$hash])) {
                return $GLOBALS['_OBJECT'][$hash];
            }
            $class_name = array_shift($param);
            if (strpos($class_name, '.') === false) {
                //$class_name = 'eqphp.' . $class_name;
            }
            $class_name = str_replace('.', '\\', $class_name);
            $reflection = new ReflectionClass($class_name);
            if ($param && $reflection->hasMethod('__construct')) {
                $GLOBALS['_OBJECT'][$hash] = $reflection->newInstanceArgs($param);
            } else {
                $GLOBALS['_OBJECT'][$hash] = $reflection->newInstance();
            }
            return $GLOBALS['_OBJECT'][$hash];
        }
        return (object)array();
    }

    //获取数组key1.key2...keyN的值
    static function array_get($data, $map = null){
        if (is_null($map)) {
            return $data;
        }
        if (is_string($map)) {
            if (isset($data[$map])) {
                return $data[$map];
            }
            $map = explode('.', $map);
        }
        foreach ($map as $segment) {
            if (is_array($data) && isset($data[$segment])) {
                $data = $data[$segment];
            } else {
                return null;
            }
        }
        return $data;
    }

    //延伸的md5方法
    static function md5($string){
        return md5($string . system::config('system.secure.key'));
    }

    //获取6位密码保护串
    static function salt(){
        $symbol = '!@#$%&?~^<>`+-*/={}[]()|_,.:;';
        $char = md5(time() . rand(100, 999));
        $salt = $char[rand(0, 31)] . $symbol[rand(0, 28)] . $char[rand(0, 31)];
        return $salt . $char[rand(0, 31)] . $symbol[rand(0, 28)] . $char[rand(0, 31)];
    }

    //返回令牌(user_agent,ip)
    static function token($string = '', $ip = ''){
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return self::md5($agent . $ip . $string);
    }

    //特殊字符过滤
    static function symbol($string, $is_strict = false){
        $risk = '~^<>`\'"\\';
        $is_strict and $risk .= '@!#$%&?+-*/={}[]()|,.:;';
        $risk = str_split($risk, 1);
        return str_replace($risk, '', $string);
    }

    //正则匹配
    static function match($string, $regexp){
        $regexp_list = self::meta('regexp');
        if (isset($regexp_list[$regexp])) {
            $regexp = $regexp_list[$regexp];
        }
        return preg_match($regexp, $string);
    }

    //获取加盐加密后的密码
    static function password($password, $salt){
        return md5(md5($password) . $salt);
    }

    //加密
    static function encrypt($string, $key = 'b335a4503870a1d1'){
        $j = 0;
        $key = md5($key);
        $buffer = $data = '';
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            if ($j == 32) {
                $j = 0;
            }
            $buffer .= $key[$j];
            $j++;
        }
        for ($i = 0; $i < $length; $i++) {
            $data .= $string[$i] ^ $buffer[$i];
        }
        return base64_encode($data);
    }

    //解密
    static function decrypt($string, $key = 'b335a4503870a1d1'){
        $string = base64_decode($string);

        $j = 0;
        $key = md5($key);
        $buffer = $data = '';
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            if ($j == 32) {
                $j = 0;
            }
            $buffer .= substr($key, $j, 1);
            $j++;
        }
        for ($i = 0; $i < $length; $i++) {
            $data .= $string[$i] ^ $buffer[$i];
        }
        return $data;
    }


}