<?php
//rely on: logger http debug
class system{

    //设置系统环境（php.ini）
    static function init(){
        //设置错误提示
        //error_reporting(config('state.error_tip') ? (E_ALL|E_STRICT) : 0);

        //session设置
        session_name('eqphp_session');
        ini_set('session.cookie_httponly',true);
        //ini_set('session.save_handler','files'); //存数据库（user）
        //session_save_path(PATH_CACHE.'session'); //设置session文件存放路径
        //ini_set('session.cookie_domain','domain.com'); //设置session域名（二级域名下session共享）
        //ini_set('session.gc_maxlifetime',1440); //周期(秒)
        //session_set_cookie_params(1440); //等价于gc_maxlifetime
        //session_cache_limiter('private'); //值为nocache时cache_expire设置无效
        //ini_set('session.cache_expire',180); //客户端cache中的有限期（分）
        //ini_set('session.use_trans_sid',0); //是否使用明码在URL中显示SID(慎用)
        //ini_set('session.use_cookies',0); //是否使用cookie在客户端保存会话ID
        ini_set('date.timezone','Asia/Chongqing'); //设置时区
        //ini_set('session.auto_start',true); //是否自动开启session，作用与session_start()相同
		
        //设置mongoDB长整形查询报错：Cannot natively represent the long
        ini_set('mongo.long_as_object',true);
		
        session_start(); //开启session
    }

    //分配目录常量
    static function directory(){
        $directory=config(null,'directory');
        foreach ($directory as $dir_name=>$dir_value) {
            foreach ($dir_value as $key=>$value) {
                define(strtoupper($dir_name.'_'.$key),PATH_ROOT.$value);
            }
        }
        self::set_url_dir(false);
    }

    //设置静态资源（前端）目录常量
    static function set_url_dir($is_view=false){
        $path=config('file','directory');
        if ($is_view) return array_map(function($directory){
            return U_R_L.$directory;
        },$path);

        foreach ($path as $key=>$value) {
            define(strtoupper('url_'.$key),U_R_L.$value);
        }
    }

    //解析路由
    static function parse_route($root_lie=0){
        $is_group=false;
        $config=config('group');
        $group=$config['list'];

        $group_name=$segment=url($root_lie);
        if (in_array($group_name,$group)) {
            $is_group=true;
            $root_lie+=1;
            $segment=url($root_lie);
            define('GROUP_NAME',$group_name);
            define('PATH_GROUP',PATH_ROOT.$group_name.'/');
        }

        if (config('state.config_route')) {
            $route=config(null,'route');
            if ($is_group && isset($route[$group_name][$segment])) {
                return explode('::',$route[$group_name][$segment]);
            }
            if (isset($route[$segment]) && strpos($route[$segment],'::')) {
                return explode('::',$route[$group_name]);
            }
        }

        if (empty($segment) || preg_match('/^[1-9]\d*$/',$segment)) {
            return array('a_index','index');
        }

        $controller='a_'.$segment;
        $method=trim(url($root_lie+1));

        if (class_exists($controller)) {
            if ($method && method_exists($controller,$method)) return array($controller,$method);
            if (method_exists($controller,'__call')) return array($controller,'__call');
            if (method_exists($controller,'__callStatic')) return array($controller,'__callStatic');
            if (method_exists($controller,'index')) return array($controller,'index');
        }
    }

    //动态调用、执行
    static function bootstrap($controller,$method){
        try{
            if ($controller && $method) {
                define('CURRENT_ACTION',substr($controller.':'.$method,2));
                if (property_exists($controller,'static_class')) {
                    if (method_exists($controller,'__before')) call_user_func($controller.'::__before');
                    call_user_func(array($controller,$method),explode('/',URL_REQUEST));
                    if (method_exists($controller,'__after')) call_user_func($controller,'::__after');
                } else {
                    $object=new $controller;
                    if (method_exists($controller,'__before')) $object->__before();
                    call_user_func_array(array($object,$method),array(explode('/',URL_REQUEST)));
                    if (method_exists($controller,'__after')) $object->__after();
                }
            } else {
                throw new Exception('absent controller or method',101);
            }
        }catch (Exception $e) {
            logger::exception('exception',$e->getCode().' : '.$e->getMessage());

            if (preg_match('/^(similar|product)$/',ENVIRONMENT)) {
                if (http::is_ajax()) {
                    http::json(array('error'=>4,'message'=>$e->getMessage(),'data'=>null));
                } else {
                    http::send(404);
                }
            }
            debug::exception($e);
        }
    }

}