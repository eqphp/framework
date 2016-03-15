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
            if (defined('GROUP_NAME') && $log->is_group_save) {
                is_dir(LOG_RUN . GROUP_NAME) or mkdir(LOG_RUN . GROUP_NAME, 0777);
                $file_name = GROUP_NAME . '/' . $file_name;
            }
            file_write(LOG_RUN . $file_name, $data, 'a+');

            //报警
            if (in_array($name, array('alert', 'collapse'))) {
                if (in_array($log->alarm['mode'], array('both', 'email'))) {
                    mail::send($log->alarm['email'], $log->alarm['title'], $data);
                }
                if (in_array($log->alarm['mode'], array('both', 'message'))) {
                    //message::send($log->alarm['phone'],$log->alarm['title'].': '.$data);
                }
            }
        }
    }

    static function exception($type, $data){
        $file = LOG_TOPIC . $type . '.log';
        $data = '[' . date('y-m-d H:i:s') . '] ' . $data . PHP_EOL;
        file_write($file, $data, 'a+');
    }

    static function sql($data, $is_read = true){
        $log_type = $is_read ? '_s.log' : '_u.log';
        $log_file = LOG_SQL . date('Y_m_d') . $log_type;
        file_write($log_file, '[' . date('H:i:s') . '] ' . $data . PHP_EOL, 'a+');
    }

    static function mongo($data, $is_read = true){
        $log_type = $is_read ? '_f.log' : '_u.log';
        $log_file = LOG_MONGO . date('Y_m_d') . $log_type;
        file_write($log_file, '[' . date('H:i:s') . '] ' . $data . PHP_EOL, 'a+');
    }

    static function mail($data){
        $file = PATH_LOG . 'mail/' . date('y-m') . '.log';
        $data = '[' . date('d H:i:s') . '] ' . $data . PHP_EOL;
        file_write($file, $data, 'a+');
    }

    static function memcache($host, $port){
        if (config('exception.memcache', 'log')) {
            $file = LOG_TOPIC . 'memcache.log';
            $data = '[' . date('H:i:s') . '] ' . $host . ':' . $port . PHP_EOL;
            file_write($file, $data, 'a+');
        }
    }

    static function visit($data){
        $log_file = LOG_VISIT . date('Y_m_d') . '.log';
        file_write($log_file, '[' . date('H:i:s') . '] ' . $data . PHP_EOL, 'a+');
    }

}