<?php
class cookie{

    static function get($key,$safe=false){
        $key=self::close_key($key,$safe);
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    static function set($key,$value,$expire=31536000,$safe=false){
        $key=self::close_key($key,$safe);
        $expire+=time();
        return setCookie($key,$value,$expire,'/',dc_domain);
    }

    static function close_key($key,$is_encrypt=false){
        $agent_info=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'eqphp';
        return $is_encrypt ? md5($key.$agent_info) : $key;
    }

}