<?php
class session{

    static function set($key,$value=null){
        if (is_array($key)) {
            foreach ($key as $k=>$v) {
                $_SESSION[cookie::close_key($k,false)]=$v;
            }
            return true;
        }
        return $_SESSION[cookie::close_key($key,false)]=$value;
    }

    static function get($key,$clear=false){
        $key=cookie::close_key($key,false);
        $value=null;
        if (isset($_SESSION[$key])) {
            $value=$_SESSION[$key];
            if ($clear) {
                unset($_SESSION[$key]);
            }
        }
        return $value;
    }

    static function clear($key=null){
        $key=cookie::close_key($key,false);
        if ($key === cookie::close_key(null,false)) {
            session_unset();
        } else {
            unset($_SESSION[$key]);
        }
    }

    static function merge($key=null,$value=false){
        if ($key === null) return self::clear(null); //删除所有
        if (is_array($key)) return self::set($key); //批量设置
        if ($value === true) return self::get($key,true); //获取后删除
        if ($value) return self::set($key,$value); //设置一个
        if ($value === null) return self::clear($key); //删除指定
        return self::get($key,false); //获取指定
    }

}