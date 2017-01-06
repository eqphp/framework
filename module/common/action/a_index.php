<?php

class a_index{

    //静态类(yes)
    private $static_class;

    //首页
    static function index(){
        self::create_directory();
        self::modify_privilege();

        http::cookie('framework_name', 'EQPHP');
        $data = array('title' => 'EQPHP Framework 3.0', 'url' => U_R_L);
        return with('view')->assign($data)->display('index.html');
    }

    //创建目录
    private static function create_directory(){
        file::folder(FILE_TEMP, 0777);

        $cache_list = array('smarty/compile/', 'compile/', 'data/ini/', 'data/json/', 'data/php/', 'data/txt/', 'data/xml/', 'session/');
        foreach ($cache_list as $path_cache) {
            file::folder(PATH_CACHE . $path_cache, 0777);
        }

        $log_list = array('trace/', 'run/', 'topic/', 'sql/', 'mongo/', 'visit/', 'test/');
        foreach ($log_list as $path_log) {
            file::folder(PATH_LOG . $path_log, 0777);
        }
    }

    //变更运行权限
    private static function modify_privilege(){
        file::modify(PATH_ROOT, 'chmod', 0755, 0644);

        file::modify(PATH_CACHE, 'chmod', 0777);
        file::modify(PATH_LOG, 'chmod', 0777);
        file::modify(PATH_DATA, 'chmod', 0777);

        file::modify(PATH_FILE, 'chmod', 0777);
        file::modify(FILE_STATIC, 'chmod', 0755, 0644);
    }


}