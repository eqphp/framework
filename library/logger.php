<?php

//rely on: system
class logger{

    static function __callStatic($name, $param){
        //读取配置文件
        $name = strtolower($name);
        $log = (object)system::config('logger');

        if ($log->store_level && in_array($name, $log->level)) {
            if (is_array($log->store_level) && !in_array($name, $log->store_level)) {
                return false;
            }

            //写日志
            $file_name = ($log->store_mode === 'mingle') ? 'log' : $name;
            list($date, $time) = explode('@', date('Y_m_d@H:i:s'));
            $file_name .= '_' . $date . '.log';
            $data = '[' . $time . '] ' . $name . ': ' . (string)$param[0] . PHP_EOL;
            if (defined('MODULE_NAME') && $log->is_module_save) {
                is_dir(LOG_RUN . MODULE_NAME) or mkdir(LOG_RUN . MODULE_NAME, 0777);
                $file_name = MODULE_NAME . '/' . $file_name;
            }
            self::record_log(LOG_RUN . $file_name, $data);

            //报警
            if (in_array($name, array('alert', 'collapse'))) {
                if (in_array($log->alarm['mode'], array('both', 'email'))) {
                    //TODO send alarm mail
                }
                if (in_array($log->alarm['mode'], array('both', 'message'))) {
                    //TODO send alarm message
                }
            }
        }
    }

    static function exception($type, $data){
        $file_name = LOG_TOPIC . $type . '.log';
        $data = '[' . date('y-m-d H:i:s') . '] ' . $data . PHP_EOL;
        self::record_log($file_name, $data);
    }

    static function visit($ip = ''){
        if (empty($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $data = '[' . date('H:i:s') . '] ' . $ip . ' ';
        if (isset($_SERVER['REQUEST_URI'])) {
            $data .= $_SERVER['REQUEST_URI'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $data .= PHP_EOL . $_SERVER['HTTP_USER_AGENT'];
        }

        $file_name = LOG_VISIT . date('Y_m_d') . '.log';
        self::record_log($file_name, $data . PHP_EOL);
    }

    static function mysql($data, $is_read = true){
        $log_type = $is_read ? '_r.log' : '_w.log';
        $file_name = LOG_MYSQL . date('Y_m_d') . $log_type;
        $data = '[' . date('H:i:s') . '] ' . $data . PHP_EOL;
        self::record_log($file_name, $data);
    }

    static function mongo($data, $is_read = true){
        $log_type = $is_read ? '_r.log' : '_w.log';
        $file_name = LOG_MONGO . date('Y_m_d') . $log_type;
        $data = '[' . date('H:i:s') . '] ' . $data . PHP_EOL;
        self::record_log($file_name, $data);
    }

    static function record_log($file_name, $data){
        $GLOBALS['_LOG'][$file_name][] = $data;
    }


}