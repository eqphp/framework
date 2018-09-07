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
        return with('view')->assign($data)->display('index');
    }

    static function composer(){
        if (is_file(PATH_LIBRARY . 'eqphp.php')) {
            unlink(PATH_LIBRARY . 'eqphp.php');
        }
        $shortcut = file_get_contents(PATH_CONFIG. ENVIRONMENT . '/shortcut.php');
        $shortcut = str_replace(array('//use eqphp',"'util::with'"),array('use eqphp',"'eqphp\\util::with'"),$shortcut);
        file_put_contents(PATH_CONFIG. ENVIRONMENT . '/shortcut.php', $shortcut);


        $buffer = '<?php'.PHP_EOL;
        $buffer .= 'namespace eqphp{'.PHP_EOL;
        $buffer .= 'use ReflectionClass, PDO, PDOException, DirectoryIterator, Memcache, Redis;'.PHP_EOL.PHP_EOL;

        /* @var $library_list */
        file::scan(PATH_LIBRARY, 'php', $library_list);
        foreach ($library_list as $library) {
            preg_match('/class(.*[\w\W]*\})$/', file_get_contents($library), $match);
            if (isset($match[0])) {
                $buffer .= $match[0] . PHP_EOL . PHP_EOL;
            }
        }

        file_put_contents(PATH_LIBRARY . 'temp.php', $buffer . '}');
        $file_detail = file::read(PATH_LIBRARY . 'temp.php');
        foreach ($file_detail as $key => $file_line) {
            //$file_line = trim($file_line);
            if (strpos($file_line,'//') === 0 || empty($file_line)) {
                unset($file_detail[$key]);
            } else {
                if ($file_line === '<?php') {
                    $file_line .= ' ';
                }
                $file_detail[$key] = $file_line;
            }
        }
        $content = trim(implode('', $file_detail));
        $content = str_replace("'system::", '\'eqphp\\system::', $content);
        $content = str_replace('//$class_name =', '$class_name =', $content);
        file_put_contents(PATH_LIBRARY . 'eqphp.php', $content);
        if (is_file(PATH_LIBRARY . 'temp.php')) {
            unlink(PATH_LIBRARY . 'temp.php');
        }
        echo 'composer completed.';
    }

    //创建目录
    private static function create_directory(){
        $cache_list = array('compile/', 'data/', 'session/');
        foreach ($cache_list as $path_cache) {
            file::folder(PATH_CACHE . $path_cache, 0777);
        }

        $log_list = array('trace/', 'run/', 'topic/', 'mysql/', 'mongo/', 'visit/', 'test/');
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