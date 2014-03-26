<?php

class s_system{

    //分配目录常量
    static function dir(){
        $server_host=isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $server_port=isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
        $server_uri=isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        $server_port=($server_port == 80) ? '' : ':'.$server_port;
        define('dc_uri','http://'.$server_host.$server_port.$server_uri);
        define('dc_request',trim($server_uri,'/'));
        define('dc_domain',$server_host);

        $dir_arr=config(null,'dir');
        foreach ($dir_arr as $dir_name=>$dir_value) {
            foreach ($dir_value as $key=>$value) {
                define('dc_'.$dir_name.'_'.$key,dc_root.$value);
            }
        }

        $url_dir=self::set_url_dir();
        foreach ($url_dir as $key=>$value) {
            define('dc_url_'.$key,$value);
        }

    }

    //配置视图环境
    static function tpl($group='home'){
        $tpl=tpl::get_instance();

        //基本配置
        $smarty=config('home','smarty');
        // $tpl->error_reporting=$smarty['error'];
        // $tpl->debugging=$smarty['debug'];
        // $tpl->allow_php_templates=$smarty['php'];

        $tpl->template_dir=$smarty['template'];
        $tpl->compile_dir=$smarty['compile'];
        $tpl->config_dir=$smarty['config'];

        $tpl->caching=$smarty['caching'];
        $tpl->cache_dir=$smarty['root'];
        $tpl->cache_lifetime=$smarty['time'];

        $tpl->left_delimiter='{';
        $tpl->right_delimiter='}';


        //网站元信息
        $meta=config('meta','site');
        $tpl->assign('meta',$meta);

        $tpl->assign('url',dc_url); //项目域名

        //设置目录常量
        $dir_data=self::set_url_dir();
        $tpl->assign('dir',$dir_data);


        //会员session
        if ($group === 'home') {
            $tpl->assign('user_id',session::get('user_id'));
        }
        //管理员session
        if ($group === 'admin') {
            $tpl->assign('admin',session::get('admin'));
        }

        if (defined('dc_group')) {
            $group_info['url']=dc_url.dc_group.'/';
            $group_info['script']=dc_url_view.dc_group.'/script/';
            $group_info['style']=dc_url_view.dc_group.'/style/';
            $group_info['image']=dc_url_view.dc_group.'/image/';
            $tpl->assign('group',$group_info);
        }

        //注册smarty组件
        $class_arr=config('plugin');
        tpl::parse_plugins($class_arr,$tpl);

        return $tpl;
    }


    //设置前端目录常量
    private static function set_url_dir(){
        $file_arr=config('file','dir');
        $view_arr=config('view','dir');
        $data_arr=config('data','dir');
        $dir_arr=array_merge($file_arr,$view_arr,$data_arr);

        $buffer=null;
        foreach ($dir_arr as $key=>$dir) {
            $buffer[$key]=dc_url.$dir;
        }
        return $buffer;
    }


    //设置系统环境（php.ini）
    static function init(){
        //设置错误提示
        error_reporting((config('state|error_tip')) ? (E_ALL|E_STRICT) : 0);

        //session设置
        // ini_set('session.auto_start',false); //是否自动开启session，作用与session_start()相同
        // session_name('');

        // ini_set('session.save_handler','files'); //存数据库（user）
        // session_save_path(dc_cache_session); //设置session文件存放路径
        // ini_set('session.cookie_domain','eq80.com'); //设置session域名（二级域名下session共享）

        // ini_set('session.gc_maxlifetime',1440); //周期(秒)
        // session_set_cookie_params(1440); //等价于gc_maxlifetime

        // session_cache_limiter('private'); //值为nocache时cache_expire设置无效
        // ini_set('session.cache_expire',180); //客户端cache中的有限期（分）

        // ini_set('session.use_trans_sid',0); //是否使用明码在URL中显示SID(慎用)
        // ini_set('session.use_cookies',0); //是否使用cookie在客户端保存会话ID
        session_start(); //开启session


        //设置时区
        ini_set('date.timezone','Asia/Chongqing');
    }


}