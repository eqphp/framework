<?php
//rely on:
class logger{

    static function __callStatic($name,$param){
        //读取配置文件
        $config=config(null,'logger');
        $name=strtolower($name);
        if ($config['store_level'] && in_array($name,$config['level'])) {
            if (is_array($config['store_level']) && !in_array($name,$config['store_level'])) {
                return true;
            }

            //写日志
            $file_name=($config['store_mode'] === 'mingle') ? 'log' : $name;
            list($date,$time)=explode('@',date('Y_m_d@H:i:s'));
            $file_name.='_'.$date.'.log';
            $data='['.$time.'] '.$name.': '.(string)$param[0].PHP_EOL;
            if (defined('GROUP_NAME') && $config['is_group_save']) {
                is_dir(LOG_RUN.GROUP_NAME) or mkdir(LOG_RUN.GROUP_NAME,0777);
                $file_name=GROUP_NAME.'/'.$file_name;
            }
            file_write(LOG_RUN.$file_name,$data,'a+');

            //报警
            if (in_array($name,array('alert','collapse'))) {
                $alarm_mode=$config['alarm_mode'];
                $alarm_title=$config['title'];
                if (in_array($alarm_mode,array('both','email'))) {
                    //mail::send($config['email'],$alarm_title,$data);
                }
                if (in_array($alarm_mode,array('both','message'))) {
                    //message::send($config['phone'],$alarm_title.': '.$data);
                }
            }
        }
    }

    static function exception($type,$info){
        $file=LOG_TOPIC.$type.'.log';
        $info='['.date('y-m-d H:i:s').'] '.$info.PHP_EOL;
        file_write($file,$info,'a+');
    }

    static function sql($info,$is_read=true){
        $log_type=$is_read ? '_s.log' : '_u.log';
        $log_file=LOG_SQL.date('Y_m_d').$log_type;
        file_write($log_file,'['.date('H:i:s').'] '.$info.PHP_EOL,'a+');
    }
	
    static function mongo($info,$is_read=true){
        $log_type=$is_read ? '_f.log' : '_u.log';
        $log_file=LOG_MONGO.date('Y_m_d').$log_type;
        file_write($log_file,'['.date('H:i:s').'] '.$info.PHP_EOL,'a+');
    }

    static function mail($info){
        $file=PATH_LOG.'mail/'.date('y-m').'.log';
        $info='['.date('d H:i:s').'] '.$info.PHP_EOL;
        file_write($file,$info,'a+');
    }

    static function memcache($host,$port){
        if (config('exception.memcache','log')) {
            $file=LOG_TOPIC.'memcache.log';
            $info='['.date('H:i:s').'] '.$host.':'.$port.PHP_EOL;
            file_write($file,$info,'a+');
        }
    }

    static function visit($info){
        $log_file=LOG_VISIT.date('Y_m_d').'.log';
        file_write($log_file,'['.date('H:i:s').'] '.$info.PHP_EOL,'a+');
    }

}