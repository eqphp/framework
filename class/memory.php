<?php

//rely on: logger
class memory{

    //memcache缓存初始化
    static function init($name = 'memcache'){
        static $cache;
        if (isset($cache) && $cache instanceof $name) {
            return $cache;
        }
        $cache = new $name;
        $config = config($name, $name);
        $cache->connect($config['host'], $config['port']);
        return $cache;
    }

    //memcache集群初始化
    static function cluster(){
        static $cache;
        if (isset($cache) && $cache instanceof Memcache) {
            return $cache;
        }

        $cache = new Memcache;
        $config = config(null, 'memcache');
        $logger = array('logger', 'memcache');
        foreach ($config as $mc) {
            list($host, $port, $weight) = array($mc['host'], $mc['port'], $mc['weight']);
            $cache->addServer($host, $port, true, $weight, 1, 15, true, $logger);
        }
        return $cache;
    }

    //redis主从集群（多服务器redis集群时ini配置里的第一项为master）
    static function group($is_master = false){
        static $cache;
        if (isset($cache) && $cache instanceof Redis) {
            return $cache;
        }

        $cache = new Redis;
        $config = config(null, 'redis');
        if ($is_master) {
            $cache->connect($config['redis']['host'], $config['redis']['port']);
        } else {
            unset($config['redis']);
            foreach ($config as $serve) {
                $cache->connect($serve['host'], $serve['port']);
            }
        }

        return $cache;
    }

    //单个快捷使用
    static function __callStatic($name, $param = array()){
        //Notice: init param
        $cache = self::init();
        return call_user_func_array(array($cache, $name), $param);
    }


}