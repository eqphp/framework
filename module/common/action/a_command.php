<?php

class a_command{


    function index(){

    }

    function create(){
        list($class, $module) = array(url(2), url(3));
        $data = '<?php' . PHP_EOL . PHP_EOL . 'class ' . $class . '{' . PHP_EOL . PHP_EOL . '}';
        if (strpos($class, 's_') === 0 or strpos($class, 'Server')) {
            if (file_exists(PATH_SERVER . $class . '.php')) {
                throw new Exception($class . ' exist in server', 184);
            }
            file_write(PATH_SERVER . '/' . $class . '.php', $data);
            exit('completed');
        }

        if (empty($module)) {
            $module = 'common';
        }
        if (strpos($class, 'a_') === 0 or strpos($class, 'Action')) {
            if (file_exists(PATH_MODULE . $module . '/action/' . $class . '.php')) {
                throw new Exception($class . ' exist in ' . $module . '/action', 184);
            }
            file_write(PATH_MODULE . $module . '/action/' . $class . '.php', $data);
            exit('completed');
        }
        if (strpos($class, 'm_') === 0 or strpos($class, 'Model')) {
            if (file_exists(PATH_MODULE . $module . '/model/' . $class . '.php')) {
                throw new Exception($class . ' exist in ' . $module . '/model', 184);
            }
            file_write(PATH_MODULE . $module . '/model/' . $class . '.php', $data);
            exit('completed');
        }
        if (strpos($class, 'p_') === 0 or strpos($class, 'Plugin')) {

            if (file_exists(PATH_MODULE . $module . '/plugin/' . $class . '.php')) {
                throw new Exception($class . ' exist in ' . $module . '/plugin', 184);
            }
            file_write(PATH_MODULE . $module . '/plugin/' . $class . '.php', $data);
            exit('completed');
        }

        if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]{1,18}$/', $class)) {
            if (file_exists(PATH_CLASS . $class . '.php')) {
                throw new Exception($class . ' exist in class', 184);
            }
            file_write(PATH_CLASS . $class . '.php', $data);
            exit('completed');
        }
        exit('nothing to do');
    }

    function map(){
        $class_map = self::parse_directory_class(PATH_CLASS);
        if ($class_map[0] !== $class_map[1]) {
            throw new Exception('have same library in class', 185);
        }
        $server_map = self::parse_directory_class(PATH_SERVER);
        if ($server_map[0] !== $server_map[1]) {
            throw new Exception('have same library in server', 185);
        }
        $module_map = self::parse_directory_class(PATH_MODULE);
        if ($module_map[0] !== $module_map[1]) {
            throw new Exception('have same library in module', 185);
        }

        $data = $class_map[2]+$server_map[2]+$module_map[2];
        $data = '<?php' . PHP_EOL . 'return ' . var_export($data, true) . ';';
        $data = str_replace('\\\\','/',$data);
        file_write(PATH_CONFIG.ENVIRONMENT.'/library_map.php',$data);
        echo 'library map created';
    }


    //清理目录、创建目录、修改权限
    function init(){
        self::clear_directory();
        self::create_directory();
        self::modify_privilege();

        echo 'init successful';
    }


    //模块创建、删除
    function module(){
        $action = url(2, '/^(create|delete)$/');
        $name = strtolower(url(3, '/^[a-z][a-z0-9_]{2,9}$/'));
        if ($action && $name) {
            if ($action === 'create') {
                //检测模块是否已存在
                if (file_exists(PATH_MODULE . $name)) {
                    throw new Exception('module already exist', 188);
                }
                foreach (array('action', 'model', 'config', 'plugin') as $category) {
                    file::folder(PATH_MODULE . $name . '/' . $category, 0777);
                }
                exit('module created');
                //注册 更改config配置module-list

            } else {
                file::delete(PATH_MODULE . $name);
                exit('module deleted');
            }
        }
        throw new Exception('param error', 189);
    }


    //清理目录
    private function clear_directory(){
        //上传临时目录
        file::delete(FILE_TEMP, true);

        //编译、缓存数据、session
        $cache_folder_list = array('smarty/compile', 'compile', 'session');
        foreach ($cache_folder_list as $cache_folder) {
            file::delete(PATH_CACHE . $cache_folder, false);
        }

        //日志topic->curl,error,exception,mail,memcache,model,mysql,redis,server
        $log_folder_list = array('mail', 'message', 'mongo', 'run', 'sql', 'run', 'test', 'topic', 'trace', 'visit');
        foreach ($log_folder_list as $log_folder) {
            file::delete(PATH_LOG . $log_folder, false);
        }
    }

    //创建目录
    private function create_directory(){
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
    private function modify_privilege(){
        file::modify(PATH_ROOT, 'chown', 'apache:apache');
        file::modify(PATH_ROOT, 'chmod', 0755, 0644);

        file::modify(PATH_CACHE, 'chmod', 0777);
        file::modify(PATH_LOG, 'chmod', 0777);
        file::modify(PATH_DATA, 'chmod', 0777);

        file::modify(PATH_FILE, 'chmod', 0777);
        file::modify(FILE_STATIC, 'chmod', 0755, 0644);
    }

    static function parse_directory_class($directory){
        $i=0;
        $class_map =array();
        file::scan($directory, 'php', $file_list);
        if ($file_list && is_array($file_list)) {
            foreach ($file_list as $file_path) {
                if (strpos($file_path,DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR) === false) {
                    $i++;
                    $class_map[basename($file_path, '.php')] = $file_path;
                }
            }
        }
        return [$i,count($class_map),$class_map];
    }


}