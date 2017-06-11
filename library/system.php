<?php

//rely on: basic input http logger debug
class system{

    //解析配置文件(mode:0-全局,1-分组)
    static function config($name, $is_module = false){
        $config = 'config/';
        $module_path = $is_module ? 'module/' . MODULE_NAME . '/' : '';
        if (preg_match('/^(local|test|similar|product)$/', ENVIRONMENT)) {
            $config .= ENVIRONMENT . '/';
        }

        //计算绝对路径
        $name = explode('.', $name);
        $file_name = PATH_ROOT . $module_path .$config . array_shift($name) . '.php';

        //环境的覆盖公共的
        if (!is_file($file_name)) {
            $file_name = str_replace('/' . ENVIRONMENT . '/', '/', $file_name);
            if (!is_file($file_name)) {
                return null;
            }
        }

        //全局变量
        $key = md5($file_name);
        if (empty($GLOBALS['_CONFIG'][$key])) {
            $GLOBALS['_CONFIG'][$key] = include($file_name);
        }
        return basic::array_get($GLOBALS['_CONFIG'][$key], $name);
    }

    //初始化系统变量
    static function init(){
        //设置错误提示
        $state = self::config('system.state');
        //error_reporting($state['error_switch'] ? E_ALL|E_STRICT : 0);
        register_shutdown_function('system::process_error');

        //设置时区
        ini_set('date.timezone', $state['timezone']);

        //mongoDB设置
        //ini_set('mongo.long_as_object',true);

        //session设置
        $session = (object)self::config('session');
        session_name($session->name);
        ini_set('session.cookie_httponly', $session->mode);
        ini_set('session.save_handler',$session->storage);
        session_save_path($session->path);
        //session_set_save_handler('ds::open', 'ds::close', 'ds::read', 'ds::write', 'ds::destroy', 'ds::gc');
        //ini_set('session.gc_maxlifetime',$session->expire);
        session_start();

        if (defined('RUN_MODE') && RUN_MODE === 'web') {
            self::web_init();
        }
    }

    //处理域名
    static private function web_init(){
        $domain = self::config('system.domain');
        list($scheme, , $port) = $domain['default'];
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], $domain['allow_host'], true)) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            throw new Exception('forbid host or domain', 100);
        }

        define('SITE_DOMAIN', $host);
        define('URL_REQUEST', trim($uri, '/'));
        define('U_R_L', $scheme . '://' . $host . $port . '/');
        define('URL_URI', U_R_L . URL_REQUEST);

        //定义当前运行模块名
        $module = self::config('system.module');
        if ($module['subdomain'] && is_array($module['subdomain'])) {
            foreach ($module['subdomain'] as $subdomain) {
                if (strpos($host, $subdomain . '.') !== false) {
                    define('SUBDOMAIN', $subdomain);
                    return define('MODULE_NAME', $subdomain);
                }
            }
        }
        $current_module = current(explode('/', trim($_SERVER['REQUEST_URI'], '/')));
        if (is_array($module['list']) && in_array($current_module, $module['list'], true)) {
            define('MODULE_NAME', $current_module);
        }
    }

    //解析路由
    static private function parse_route($mode = 'snake_load', $root_lie = 0){
        $is_module = false;
        $space_name = 'common';
        $module = $segment = input::url($root_lie);
        if (defined('MODULE_NAME') || defined('SUBDOMAIN')) {
            $is_module = true;
            $module = MODULE_NAME;
            $space_name = MODULE_NAME;
            if ($module === $segment) {
                $root_lie += 1;
                $segment = input::url($root_lie);
            }
        }

        if (self::config('system.state.config_route')) {
            $route = self::config('route');
            if ($is_module && isset($route[$module][$segment])) {
                return explode('::', $route[$module][$segment]);
            }
            if (isset($route[$segment]) && is_string($route[$segment]) && strpos($route[$segment], '::')) {
                return explode('::', $route[$module]);
            }
        }

        $prefix = $suffix = '';
        if ($mode === 'snake_load') {
            $prefix = 'a_';
        }
        if ($mode === 'camelLoad') {
            $suffix = 'Action';
        }
        if ($mode === 'composer') {
            $prefix = $space_name . '\action\\';
        }

        if (empty($segment) || preg_match('/^[1-9]\d*$/', $segment)) {
            $action_name = $module ? $module : 'index';
            return array($prefix . $action_name . $suffix, 'index');
        }

        $controller = $prefix . $segment . $suffix;
        $method = trim(input::url($root_lie + 1));

        if (class_exists($controller)) {
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
    static function bootstrap($mode = 'snake_load'){
        try {
            self::init();
            list($controller, $method) = self::parse_route($mode);
            if (empty($controller) || empty($method)) {
                throw new Exception('absent controller or method', 101);
            }

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

    //注册表方式自动加载
    static function map_load($class){
        if (empty($GLOBALS['_LIBRARY'][$class])) {
            $GLOBALS['_LIBRARY'][$class] = self::config('library_map.' . $class);
            if ($GLOBALS['_LIBRARY'][$class]) {
                return include $GLOBALS['_LIBRARY'][$class];
            }
        }
        return false;
    }

    //snake_case风格类库自动加载
    static function snake_load($class){
        $prefix = substr($class, 0, strpos($class, '_'));
        $option = array('a' => 'action', 'm' => 'model');
        if (in_array($prefix, array_keys($option), true)) {
            $part = $option[$prefix] . '/' . $class . '.php';
            if (defined('MODULE_NAME')) {
                $load_file = PATH_ROOT . 'module/' . MODULE_NAME . '/' . $part;
                if (is_file($load_file)) {
                    return include $load_file;
                }
            }
            $load_file = PATH_ROOT . 'module/common/' . $part;
            if (is_file($load_file)) {
                return include $load_file;
            }

            if ($prefix === 'a') {
                logger::notice('class [' . $class . '] not found');
                http::send(404);
            }
        }

        if ($prefix === 's') {
            $load_file = PATH_ROOT . 'server/' .$class . '.php';
            if (is_file($load_file)) {
                return include $load_file;
            }
        }

        $load_file = PATH_ROOT . 'library/' .$class . '.php';
        if (is_file($load_file)) {
            return include $load_file;
        }

        //贪婪加载
        if (self::config('system.state.greedy_load')) {
            $search_directory = $prefix === 's' ? 'server' : 'library';
            /* @var $file_list */
            if ($load_file = file::search(PATH_ROOT . $search_directory, $class, $file_list, true)) {
                return include $load_file;
            }
        }

        if (strpos($class,'Smarty_Internal') === false){
            logger::error('class [' . $class . '] not found');
        }
    }

    //camelCase风格类库自动加载
    static function camelLoad($class){
        if (preg_match('/(Action|Model)$/',$class,$match)) {
            $part = strtolower($match[1]) . '/' . $class . '.php';
            if (defined('MODULE_NAME')) {
                $load_file = PATH_ROOT . 'module/' . MODULE_NAME . '/' . $part;
                if (is_file($load_file)) {
                    return include $load_file;
                }
            }
            $load_file = PATH_ROOT . 'module/common/' . $part;
            if (is_file($load_file)) {
                return include $load_file;
            }

            if ($match[1] === 'Action') {
                logger::notice('class [' . $class . '] not found');
                http::send(404);
            }
        }

        //业务类
        if (strrpos($class,'Server')) {
            $load_file = PATH_ROOT . 'server/' .$class . '.php';
            if (is_file($load_file)) {
                return include $load_file;
            }
        }

        //工具类
        $load_file = PATH_ROOT . 'library/' . strtolower($class) . '.php';
        if (is_file($load_file)) {
            return include $load_file;
        }

        //贪婪加载(工具类、业务类)
        if (self::config('system.state.greedy_load')) {
            if (strrpos($class, 'Server')) {
                $search_directory = 'server';
            } else {
                $search_directory = 'library';
                $class = strtolower($class);
            }
            /* @var $file_list */
            if ($load_file = file::search(PATH_ROOT . $search_directory, $class, $file_list, true)) {
                return include $load_file;
            }
        }

        if (strpos($class,'Smarty_Internal') === false){
            logger::error('class [' . $class . '] not found');
        }
    }

    //处理错误
    static function process_error(){
        $error = (object)error_get_last();
        if ($error && isset($error->type)) {
            ob_end_clean();
            $error->type = basic::meta('error.' . $error->type);
            $log_data = $error->type . ' : ' . $error->message . ' [' . $error->file . ' - ' . $error->line . ']';
            logger::exception('error', $log_data);

            if (defined('RUN_MODE') && RUN_MODE === 'cli') {
                http::quit($log_data);
            }

            if (preg_match('/^(similar|product)$/', ENVIRONMENT)) {
                if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
                    if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === 'xmlhttprequest') {
                        header('Content-Type:application/json; charset=utf-8');
                        http::quit(json_encode(array('error' => 4, 'message' => $error->message)));
                    }
                }
                header('location: ' . U_R_L . 'abort/error');
                http::quit();
            }

            header('Content-Type:text/html; charset=utf-8');
            $html = '<link rel="stylesheet" type="text/css" href="/file/static/style/basic.css">';
            $html .= '<div class="trace"><pre><h5><b>%s</b>%s</h5><h6>%s<b>%d</b></h6></pre></div>';
            http::quit(sprintf($html, $error->type, $error->message, $error->file, $error->line));
        }
        http::quit();
    }


}