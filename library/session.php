<?php

//rely on: basic
class session{

    static function set($key, $value = null){
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $_SESSION[$name] = $value;
            }
        } elseif (strpos($key, '.') !== false) {
            basic::array_set($_SESSION, $key, $value);
        } else {
            $_SESSION[$key] = $value;
        }
        return true;
    }

    static function get($key, $clear = false){
        $value = basic::array_get($_SESSION, $key);
        $clear and basic::array_unset($_SESSION, $key);
        return $value;
    }

    static function clear($key = null){
        if (is_null($key)) {
            session_unset();
        } elseif (is_array($key)) {
            foreach ($key as $k) {
                unset($_SESSION[$k]);
            }
        } elseif (strpos($key, '.') !== false) {
            basic::array_unset($_SESSION, $key);
        } else {
            unset($_SESSION[$key]);
        }
        return true;
    }

    static function merge($key = null, $value = false){
        //删除所有
        if ($key === null) {
            return self::clear(null);
        }
        //批量设置
        if (is_array($key)) {
            if (is_null($value)) {
                return self::clear($key);
            }
            return self::set($key, null);
        }
        //获取后删除
        if ($value === true) {
            return self::get($key, true);
        }
        //设置一个
        if ($value) {
            return self::set($key, $value);
        }
        //删除指定
        if ($value === null) {
            return self::clear($key);
        }
        //获取指定
        return self::get($key, false);
    }

}