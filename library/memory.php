<?php

//rely on: system
class memory{

    //memcache缓存初始化
    static function init($name = 'redis'){
        static $cache;
        if (isset($cache) && $cache instanceof $name) {
            return $cache;
        }
        $cache = new $name;
        $config = system::config($name . '.' . $name);
        $cache->connect($config['host'], $config['port']);
        if ($name === 'redis') {
            $cache->auth($config['password']);
            $cache->setOption(Redis::OPT_PREFIX, $config['prefix']);
            $cache->select($config['database']);
        }
        return $cache;
    }

    //memcache集群初始化
    static function cluster(){
        static $cache;
        if (isset($cache) && $cache instanceof Memcache) {
            return $cache;
        }

        $cache = new Memcache;
        $config = system::config('memcache');
        foreach ($config as $mc) {
            list($host, $port, $weight) = array($mc['host'], $mc['port'], $mc['weight']);
            $cache->addServer($host, $port, true, $weight, 1, 15, true, array(__CLASS__, 'memcache_logger'));
        }
        return $cache;
    }

    //redis主从集群
    static function group($is_master = false){
        static $cache;
        if (isset($cache) && $cache instanceof Redis) {
            return $cache;
        }

        $cache = new Redis;
        $config = system::config('redis');
        if ($is_master) {
            $cache->connect($config['redis']['host'], $config['redis']['port']);
            $cache->auth($config['redis']['password']);
            $cache->setOption(Redis::OPT_PREFIX, $config['redis']['prefix']);
            $cache->select($config['redis']['database']);
        } else {
            unset($config['redis']);
            foreach ($config as $serve) {
                $cache->connect($serve['host'], $serve['port']);
                $cache->auth($serve['password']);
                $cache->setOption(Redis::OPT_PREFIX, $serve['prefix']);
                $cache->select($serve['database']);
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


    //记录memcache异常日志
    static function memcache_logger($host, $port){
        $file_name = LOG_TOPIC . 'memcache.log';
        $data = '[' . date('H:i:s') . '] ' . $host . ':' . $port . PHP_EOL;
        file_put_contents($file_name, $data, FILE_APPEND);
    }

}