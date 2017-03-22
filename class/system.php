<?php

//rely on: logger http debug
class system{

    //初始化系统变量
    static function init(){
        //设置错误提示
        $state=config('state','config');
        //error_reporting($state['error_switch'] ? E_ALL|E_STRICT : 0);

        //设置时区
        ini_set('date.timezone', $state['timezone']);

        //mongoDB设置
        //ini_set('mongo.long_as_object',true);

        //session设置
        $session=(object)config(null,'session');
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

        //设置目录常量、调试常亮
        self::define_directory_constant();
        debug::set_debug_constant();
    }

    //处理域名
    static function web_init(){
        $domain = config('domain');
        $allow_host = $domain['allow_host'];
        $allow_port = $domain['allow_port'];
        list($scheme, $host, $port) = $domain['default'];
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === '1' || strtolower($_SERVER['HTTPS']) === 'on'))
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443')) {
            $scheme = 'https';
        }
        if (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], $allow_host)) {
            $host = $_SERVER['HTTP_HOST'];
        }
        if (isset($_SERVER['SERVER_PORT']) && in_array(intval($_SERVER['SERVER_PORT']), $allow_port)) {
            $port = intval($_SERVER['SERVER_PORT']);
        }

        define('SITE_DOMAIN', $host);
        define('URL_REQUEST', trim($uri, '/'));
        $port = ($port === 80) ? '' : ':' . $port;
        define('U_R_L', $scheme . '://' . $host . $port . '/');
        define('URL_URI', U_R_L . URL_REQUEST);
        foreach (config('file', 'directory') as $key => $value) {
            define(strtoupper('url_' . $key), U_R_L . $value);
        }

        //定义当前运行模块名
        $module = config('module');
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

    //设置目录常量
    static function define_directory_constant(){
        $directory = config(null, 'directory');
        foreach ($directory as $dir_name => $dir_value) {
            foreach ($dir_value as $key => $value) {
                define(strtoupper($dir_name . '_' . $key), PATH_ROOT . $value);
            }
        }
    }

    //构建静态资源（前端）目录常量
    static function build_view_directory(){
        return array_map(function ($directory){
            return U_R_L . $directory;
        }, config('file', 'directory'));
    }

    //解析路由
    static function parse_route($is_camel = false, $root_lie = 0){
        $is_module = false;
        $module = $segment = url($root_lie);
        if (defined('MODULE_NAME') || defined('SUBDOMAIN')) {
            $is_module = true;
            $module = MODULE_NAME;
            if ($module === $segment) {
                $root_lie += 1;
                $segment = url($root_lie);
            }
        }

        if (config('state.config_route')) {
            $route = config(null, 'route');
            if ($is_module && isset($route[$module][$segment])) {
                return explode('::', $route[$module][$segment]);
            }
            if (isset($route[$segment]) && is_string($route[$segment]) && strpos($route[$segment], '::')) {
                return explode('::', $route[$module]);
            }
        }

        list($prefix, $suffix) = $is_camel ? array('', 'Action') : array('a_', '');
        if (empty($segment) || preg_match('/^[1-9]\d*$/', $segment)) {
            $action_name = $module ? $module : 'index';
            return array($prefix . $action_name . $suffix, 'index');
        }

        $controller = $prefix . $segment . $suffix;
        $method = trim(url($root_lie + 1));

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

    //处理错误
    static function process_error(){
        $error = (object)error_get_last();
        if ($error && isset($error->type)) {
            ob_end_clean();
            $type = config($error->type, 'error');
            $log_data = $type . ' : ' . $error->message . ' [' . $error->file . ' - ' . $error->line . ']' . PHP_EOL;
            if (defined('RUN_MODE') && RUN_MODE === 'cli') {
                return file_write(LOG_CRONTAB . 'error.log', $log_data, 'a+');
            }
            file_write(LOG_TOPIC . 'error.log', $log_data, 'a+');

            if (preg_match('/^(similar|product)$/', ENVIRONMENT)) {
                if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])){
                    if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === 'xmlhttprequest') {
                        header('Content-Type:application/json; charset=utf-8');
                        quit(json_encode(array('error' => 4, 'message' => $error->message, 'data' => null)));
                    }
                }
                header('location: ' . U_R_L . 'abort/error');
                quit();
            }

            header('Content-Type:text/html; charset=utf-8');
            $html = '<link rel="stylesheet" type="text/css" href="/file/static/style/basic.css">';
            $html .= '<div class="trace"><pre>';
            $html .= sprintf('<h5><b>%s</b>%s</h5>', $type, $error->message);
            $html .= sprintf('<h6>%s<b>%s</b></h6>', $error->file, $error->line);
            $html .= '</pre></div>';
            quit($html);
        }
    }

    //snake_case风格类库自动加载
    static function snake_case_auto_load($class){
        $prefix = substr($class, 0, strpos($class, '_'));
        if (in_array($prefix, array('a', 'm', 'p'), true)) {
            $option=array('a'=>'action', 'm'=>'model', 'p'=>'plugin');
            if (defined('MODULE_NAME')) {
                $load_file = PATH_ROOT . 'module/' . MODULE_NAME . '/' .$option[$prefix] . '/' . $class . '.php';
                if (file_exists($load_file)) {
                    return include $load_file;
                }
            }

            //通用加载
            if (config('state.common_load')) {
                $load_file = PATH_ROOT . 'module/common/' . $option[$prefix] . '/' .$class . '.php';
                if (file_exists($load_file)) {
                    return include $load_file;
                }
            }

            if ($prefix === 'a') {
                logger::notice('class [' . $class . '] not found');
                http::send(404);
            }
        }

        if ($prefix === 's') {
            $load_file = PATH_ROOT . 'server/' .$class . '.php';
            if (file_exists($load_file)) {
                return include $load_file;
            }
        }

        $load_file = PATH_ROOT . 'class/' .$class . '.php';
        if (file_exists($load_file)) {
            return include $load_file;
        }

        //贪婪加载
        if (config('state.greedy_load')) {
            $search_directory = $prefix === 's' ? 'server' : 'class';
            if ($load_file = file::search(PATH_ROOT . $search_directory, $class, $file_list, true)) {
                return include $load_file;
            }
        }

        if (strpos($class,'Smarty_Internal') === false){
            logger::error('class [' . $class . '] not found');
        }
    }

    //camelCase风格类库自动加载
    static function camelCaseAutoLoad($class){
        if (preg_match('/(Action|Model|Plugin)$/',$class,$match)) {
            if (defined('MODULE_NAME')) {
                $load_file = PATH_ROOT . 'module/' . MODULE_NAME . '/' . strtolower($match[1]) . '/' . $class . '.php';
                if (file_exists($load_file)) {
                    return include $load_file;
                }
            }

            //通用加载
            if (config('state.common_load')) {
                $load_file = PATH_ROOT . 'module/common/' . strtolower($match[1]). '/' . $class . '.php';
                if (file_exists($load_file)) {
                    return include $load_file;
                }
            }

            if ($match[1] === 'Action') {
                logger::notice('class [' . $class . '] not found');
                http::send(404);
            }
        }

        //业务类
        if (strrpos($class,'Server')) {
            $load_file = PATH_ROOT . 'server/' .$class . '.php';
            if (file_exists($load_file)) {
                return include $load_file;
            }
        }

        //工具类
        $load_file = PATH_ROOT . 'class/' . strtolower($class) . '.php';
        if (file_exists($load_file)) {
            return include $load_file;
        }

        //贪婪加载(工具类、业务类)
        if (config('state.greedy_load')) {
            if (strrpos($class, 'Server')) {
                $search_directory = 'server';
            } else {
                $search_directory = 'class';
                $class = strtolower($class);
            }
            if ($load_file = file::search(PATH_ROOT . $search_directory, $class, $file_list, true)) {
                return include $load_file;
            }
        }

        if (strpos($class,'Smarty_Internal') === false){
            logger::error('class [' . $class . '] not found');
        }
    }

    //注册表方式自动加载
    static function path_map_auto_load($class){
        if (empty($GLOBALS['_LIBRARY'][$class])) {
            $GLOBALS['_LIBRARY'][$class] = config($class,'library_map');
            if ($GLOBALS['_LIBRARY'][$class]) {
                return include $GLOBALS['_LIBRARY'][$class];
            }
        }
        return false;
    }



}