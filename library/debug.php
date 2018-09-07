<?php

//rely on: help html
class debug{

    //开发模式-调试方法
    static function out($param = 'ok', $mode = 1, $is_exit = true){
        headers_sent() or header('Content-Type:text/html; charset=utf-8');
        echo '<pre>' . PHP_EOL;
        $method = array('var_dump', 'print_r', 'var_export');
        $method[$mode]($param);
        $is_exit ? exit('</pre>') : print('</pre>');
    }

    //追溯方法
    static function trace($data = '', $file_name = ''){
        is_object($data) and $data = help::object_array($data);
        is_array($data) and $data = print_r($data, true);
        empty($file_name) and $file_name = date('Y_m_d') . '.log';
        file_put_contents(LOG_TRACE . $file_name, $data . PHP_EOL, FILE_APPEND);
    }

    //取得微秒数、内存消耗
    static function flag(&$flag){
        $flag = array(round(microtime(true) * 1000), memory_get_usage());
    }

    //计算运行时间,内存消耗
    static function used($begin, $end){
        $diff_time = round(($end[0] - $begin[0])/1000, 3);
        $diff_memory = round(($end[1] - $begin[1]) / 128, 3);
        $max_memory = round((memory_get_peak_usage()) / 128, 3);
        return array($diff_time . 's', $diff_memory . 'kb', $max_memory . 'kb');
    }

    //获取系统信息
    static function info($type = 1){
        $data = array('basic', 'const', 'variable', 'function', 'class', 'interface', 'file');
        if (is_numeric($type) && $type < 7) {
            $type = $data[$type];
        }
        switch ($type) {
            case 'const':
                $const_list = get_defined_constants(true);
                return $const_list['user'];
            //2因作用域，请在外边直接调用函数
            case 'variable':
                return 'please use: get_defined_vars()';
            case 'function':
                $function_list = get_defined_functions();
                return $function_list['user'];
            case 'class':
                return array_slice(get_declared_classes(), 125);
            case 'interface':
                return array_slice(get_declared_interfaces(), 10);
            case 'file':
                return get_included_files();
            default:
                return array(
                    'version'=>'3.6.0',
                    'system'=>php_uname(),
                    'service'=>php_sapi_name(),
                    'php_version'=>PHP_VERSION,
                    'magic_quotes'=>get_magic_quotes_gpc(),
                    'time_zone'=>date_default_timezone_get(),
                );
        }
    }

}