<?php

//rely on: logger http debug
class system{

    //初始化系统变量
    static function init(){
        //设置错误提示
        $state=config('state','config');
        //error_reporting($state['error_switch'] ? E_ALL|E_STRICT : 0);

        //设置目录常量
        self::set_system_constant();

        //设置时区
        ini_set('date.timezone', $state['timezone']);

        //mongoDB设置
        //ini_set('mongo.long_as_object',true);

        //session设置
        $session=(object)config(null,'session');
        session_name($session->name);
        ini_set('session.cookie_httponly', $session->mode);
        //ini_set('session.save_handler',$session->storage);
        //session_save_path($session->path);
        //session_set_save_handler('ds::open', 'ds::close', 'ds::read', 'ds::write', 'ds::destroy', 'ds::gc');
        //ini_set('session.gc_maxlifetime',$session->expire);
        session_start();
    }

    //设置目录常量
    static function set_system_constant(){
        $directory = config(null, 'directory');
        foreach ($directory as $dir_name => $dir_value) {
            foreach ($dir_value as $key => $value) {
                define(strtoupper($dir_name . '_' . $key), PATH_ROOT . $value);
            }
        }
        self::set_url_dir(false);
    }

    //设置静态资源（前端）目录常量
    static function set_url_dir($is_view = false){
        $path = config('file', 'directory');
        if ($is_view) {
            return array_map(function ($directory){
                return U_R_L . $directory;
            }, $path);
        }

        foreach ($path as $key => $value) {
            define(strtoupper('url_' . $key), U_R_L . $value);
        }
    }

    //解析路由
    static function parse_route($root_lie = 0){
        $is_group = false;
        $config = config('group');
        $group = $config['list'];

        $group_name = $segment = url($root_lie);
        if (in_array($group_name, $group)) {
            $is_group = true;
            $root_lie += 1;
            $segment = url($root_lie);
            define('GROUP_NAME', $group_name);
            define('PATH_GROUP', PATH_ROOT . $group_name . '/');
        }

        if (config('state.config_route')) {
            $route = config(null, 'route');
            if ($is_group && isset($route[$group_name][$segment])) {
                return explode('::', $route[$group_name][$segment]);
            }
            if (isset($route[$segment]) && strpos($route[$segment], '::')) {
                return explode('::', $route[$group_name]);
            }
        }

        if (empty($segment) || preg_match('/^[1-9]\d*$/', $segment)) {
            return array('a_index', 'index');
        }

        $controller = 'a_' . $segment;
        $method = trim(url($root_lie + 1));

        if (class_exists($controller)) {
            if (in_array('a_restful', class_parents($controller))) {
                $method = strtolower($_SERVER['REQUEST_METHOD']);
                $regexp = '/^(GET|POST|PATCH|PUT|DELETE|HEAD|OPTIONS)$/i';
                if (!preg_match($regexp, $method)) {
                    http::send(405, false);
                }
            }
            if ($method && method_exists($controller, $method)) {
                return array($controller, $method);
            }
            if (method_exists($controller, '__call')) {
                return array($controller, '__call');
            }
            if (method_exists($controller, '__callStatic')) {
                return array($controller, '__callStatic');
            }
            if (method_exists($controller, 'index')) {
                return array($controller, 'index');
            }
        }
    }

    //动态调用、执行
    static function bootstrap($controller, $method){
        try {
            if ($controller && $method) {
                define('CURRENT_ACTION', substr($controller . ':' . $method, 2));
                if (property_exists($controller, 'static_class')) {
                    if (method_exists($controller, '__before')) {
                        call_user_func($controller . '::__before');
                    }
                    call_user_func(array($controller, $method), explode('/', URL_REQUEST));
                    if (method_exists($controller, '__after')) {
                        call_user_func($controller . '::__after');
                    }
                } else {
                    $object = new $controller;
                    if (method_exists($controller, '__before')) {
                        $object->__before();
                    }
                    call_user_func_array(array($object, $method), array(explode('/', URL_REQUEST)));
                    if (method_exists($controller, '__after')) {
                        $object->__after();
                    }
                }
            } else {
                throw new Exception('absent controller or method', 101);
            }
        } catch (Exception $e) {
            logger::exception('exception', $e->getCode() . ' : ' . $e->getMessage());
            if (preg_match('/^(similar|product)$/', ENVIRONMENT)) {
                if (http::is_ajax()) {
                    http::json(array('error' => 4, 'message' => $e->getMessage(), 'data' => null));
                } else {
                    http::abort($e->getMessage(), '', 10);
                }
            }
            debug::exception($e);
        }
    }

}