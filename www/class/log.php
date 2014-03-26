<?php

class log{

    /***错误、异常记载***********************/

    static function exception($type,$info){
        $file=dc_log_exception.$type.'.log';
        $info='['.date('y-m-d H:i:s').'] '.$info."\r";
        file::save($file,$info,'a+');
    }

    static function memcache($host,$port){
        $file=dc_log_exception.'memcache.log';
        $info='['.date('H:i:s').'] '.$host.':'.$port."\n";
        file::save($file,$info,'a+');
    }


    /***日志记录***********************/

    static function sql($info,$is_read=true){
        $log_type=$is_read ? '_s.log' : '_u.log';
        $log_file=dc_log_sql.date('Y_m_d').$log_type;
        file::save($log_file,'['.date('H:i:s').'] '.$info."\r",'a+');
    }

    static function mail($info){
        $file=dc_log_log.'mail/'.date('y-m').'.log';
        $info='['.date('d H:i:s').'] '.$info."\n";
        file::save($file,$info,'a+');
    }


}