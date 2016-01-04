<?php
//rely on: secure
class session{

    static function set($key,$value=null,$is_secret_key=false){

        if (is_array($key)) {
            foreach ($key as $name=>$value) {
                $is_secret_key and $name=secure::token($name);
                $_SESSION[$name]=$value;
            }
            return true;
        }
        $is_secret_key and $key=secure::token($key);
        return $_SESSION[$key]=$value;
    }

    static function get($key,$clear=false,$is_secret_key=false){
        $is_secret_key and $key=secure::token($key);
        $value=null;
        if (isset($_SESSION[$key])) {
            $value=$_SESSION[$key];
            if ($clear) unset($_SESSION[$key]);
        }
        return $value;
    }

    static function clear($key=null,$is_secret_key=false){
        $is_secret_key and $key=secure::token($key);
        if (is_null($key)) {
            session_unset();
        } elseif (is_array($key)) {
            foreach ($key as $k) unset($_SESSION[$k]);
        } else {
            unset($_SESSION[$key]);
        }
        return true;
    }

    static function merge($key=null,$value=false,$is_secret_key=false){
        if ($key === null) return self::clear(null); //删除所有
        if (is_array($key)) return self::set($key,null,$is_secret_key); //批量设置
        if ($value === true) return self::get($key,true,$is_secret_key); //获取后删除
        if ($value) return self::set($key,$value,$is_secret_key); //设置一个
        if ($value === null) return self::clear($key,$is_secret_key); //删除指定
        return self::get($key,false,$is_secret_key); //获取指定
    }

}