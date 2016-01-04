<?php
//rely on: secure regexp
class input{

    //接收处理get传值
    static function get($name,$mode='get'){
        //是否初始化
        if ($mode === 'isset') return isset($_GET[$name]);
        if (isset($_GET[$name])) {
            return self::process($_GET[$name],$mode);
        }
    }

    //接收处理POST传值
    static function post($name,$mode='post'){
        //是否初始化
        if ($mode === 'isset') return isset($_POST[$name]);
        if (isset($_POST[$name])) {
            return self::process($_POST[$name],$mode);
        }
    }

    //REQUEST传值
    static function request($name,$mode='request'){
        if ($mode === 'isset') return isset($_REQUEST[$name]);
        if (isset($_REQUEST[$name])) {
            return self::process($_REQUEST[$name],$mode);
        }
    }

    //SERVER传值
    static function server($name,$mode='server'){
        $name=strtoupper($name);
        if ($mode === 'isset') return isset($_SERVER[$name]);
        if (isset($_SERVER[$name])) {
            return self::process($_SERVER[$name],$mode);
        }
    }

    //COOKIE设置、获取
    static function cookie(){
        $param=func_get_args();

        if (func_num_args() === 1) {
            $key=$param[0];
            if (is_array($param[0])) {
                $key=$param[0][0];
                if (isset($param[0][1]) && $param[0][1]) {
                    $key=secure::token($param[0][0]);
                }
            }
            if (isset($_COOKIE[$key])) {
                if (is_numeric($_COOKIE[$key]) && $_COOKIE[$key] <= 2147483647) {
                    return $_COOKIE[$key]+0;
                }
                return secure::symbol(trim(htmlspecialchars(strip_tags($_COOKIE[$key]))));
            }
            return null;
        }

        list($key,$value)=$param;
        $expire=isset($param[2]) ? $param[2] : 31536000;
        if (isset($param[3]) && $param[3]) {
            $key=secure::token($key);
        }
        $expire+=time();
        return setCookie($key,$value,$expire,'/',trim(SITE_DOMAIN,'www.'));
    }

    //对输入值处理
    private static function process($value,$mode){
        //原味输出
        $input_type=array('get','post','request','server');
        if (in_array($mode,$input_type,true)) return $value;
        //trim过滤、魔术引号转义
        if (is_string($value)) {
            $value=get_magic_quotes_gpc() ? trim($value) : trim(addslashes($value));
        }
        switch($mode){
            case 'title'://标题、关键词(去空、特殊字符、html标签)
                return trim(htmlspecialchars(strip_tags($value)));
            case 'int'://ID,自然数、POST的整型(0-N,ID、number)
                return abs((int)($value));
            case 'text'://介绍、详细内容(就留允许的html标签)
                $allow_tags='<ul><ol><li><p><h1><h2><h3><h4><h5><h6><table><tr><th><td>';
                $allow_tags.='<a><img><span><b><i><em><cite><strong><br><hr>';
                return trim(htmlspecialchars(strip_tags($value,$allow_tags)));
            case 'number'://数字
                return regexp::match($value,'number') ? $value : 0;
            case 'float'://小数、浮点数(货币、概率)
                return (float)($value);
            case 'account'://邮箱、用户名(注册账号时不区分大小写)
                return trim(secure::symbol(strip_tags(strtolower($value))));
            case 'date'://日期
            case 'time'://时间
            case 'date_time'://日期时间
                $option=array('date'=>'Y-m-d','time'=>'H:i:s','date_time'=>'Y-m-d H:i:s');
                $format_time=date($option[$mode],strtotime($value));
                return ($format_time === $value) ? $value : null;
            case 'many'://联合复选框(checkbox)
                return implode(',',$value);
            default://正则匹配输出
                return regexp::match($value,$mode) ? $value : null;
        }
    }

    //批量安全过滤、接收数据
    static function filter($data,$type='post'){
        if (is_array($data)) foreach ($data as $name=>$rule) {
            $buffer[$name]=self::$type($name,$rule);
        }
        return isset($buffer) ? $buffer : array();
    }
}