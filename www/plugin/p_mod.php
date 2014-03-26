<?php

class p_mod{

    //解析配置文件的常量
    static function constant($key,$option,$file='const'){
        return config($option.'|'.$key,$file);
    }

    //格式化日期时间
    static function format_time($time,$mode=1){
        if ($mode == 1) return date('Y年n月j日',strtotime($time));
        if ($mode == 2) return date('Y-n-j',strtotime($time));
    }

    //智能时间
    static function cut_time($time){
        $s_time=strtotime(date('Y-m-d H:i:s'))-strtotime($time);
        $m_time=$s_time/2592000;
        $hi_time=date('H:i',strtotime($time));
        $t_date=date('Y-m-d',strtotime($time));
        if ($s_time < 1) return '刚刚';
        if ($s_time <= 60) return intval($s_time).'秒前';
        if ($s_time <= 3600) return intval($s_time/60).'分钟前';
        if ($t_date == date('Y-m-d')) return '今天'.$hi_time;
        if ($t_date == date("Y-m-d",strtotime("-1 day"))) return '昨天'.$hi_time;
        if ($t_date == date("Y-m-d",strtotime("-2 day"))) return '前天'.$hi_time;
        if ($s_time <= 2592000) return intval($s_time/86400).'天前';
        if ($m_time <= 12) return intval($m_time).'个月前';
        return $t_date;
    }


    /******************************
    //++++项目实际使用modifier++++//
     *******************************/

    static function message_parse_access($access){
        if ($access) {
            $access_option=explode(',',$access);
            $access_list=config('access_list','message_admin');

            return s_extend::arr_act($access_option,$access_list,'&#13;');
        }
    }

}