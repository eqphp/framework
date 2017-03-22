<?php

//rely on:
class logger{

    static function __callStatic($name, $param){
        //读取配置文件
        $name = strtolower($name);
        $log = (object)config(null, 'logger');

        if ($log->store_level && in_array($name, $log->level)) {
            if (is_array($log->store_level) && !in_array($name, $log->store_level)) {
                return true;
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
            file_write(LOG_RUN . $file_name, $data, 'b');

            //报警
            if (in_array($name, array('alert', 'collapse'))) {
                if (in_array($log->alarm['mode'], array('both', 'email'))) {
                    with('mail')->take(array($log->alarm['title'],$data))->send($log->alarm['email']);
                }
                if (in_array($log->alarm['mode'], array('both', 'message'))) {
                    with('message')->message($log->alarm['title'].': '.$data)->send($log->alarm['phone']);
                }
            }
        }
    }

    static function exception($type, $data){
        $file = LOG_TOPIC . $type . '.log';
        $data = '[' . date('y-m-d H:i:s') . '] ' . $data . PHP_EOL;
        file_write($file, $data, 'a+');
    }

    static function visit(){
        $log_file = LOG_VISIT . date('Y_m_d') . '.log';
        $data = '[' . date('H:i:s') . '] ' . help::ip() . ' ';
        if (isset($_SERVER['REQUEST_URI'])) {
            $data .= $_SERVER['REQUEST_URI'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $data .= PHP_EOL . $_SERVER['HTTP_USER_AGENT'];
        }
        file_write($log_file, $data . PHP_EOL, 'a+');
    }


    static function mysql($data, $is_read = true){
        $log_type = $is_read ? '_r.log' : '_w.log';
        $log_file = LOG_MYSQL . date('Y_m_d') . $log_type;
        file_write($log_file, '[' . date('H:i:s') . '] ' . $data . PHP_EOL, 'a+');
    }

    static function mongo($data, $is_read = true){
        $log_type = $is_read ? '_r.log' : '_w.log';
        $log_file = LOG_MONGO . date('Y_m_d') . $log_type;
        file_write($log_file, '[' . date('H:i:s') . '] ' . $data . PHP_EOL, 'a+');
    }




}