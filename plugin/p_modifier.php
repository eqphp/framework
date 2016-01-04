<?php

class p_modifier{

    //解析配置文件的常量
    static function constant($name,$file='const',$mode=0){
        return config($name,$file,$mode);
    }

    //截取指定长度的中英文混合字符串
    static function truncate($data,$length,$from=0){
        return basic::truncate($data,$length,$from);
    }

    //掩藏用户敏感信息
    static function mask($data,$type='phone'){
        if (empty($data)) return '';
        switch($type) {
        case 'phone':
            return substr_replace($data,'****',3,4);
        case 'email':
            list($prefix,$suffix)=explode('@',$data);
            return substr_replace($prefix,'****',2,4).'@'.$suffix;
        case 'qq':
            return substr_replace($data,'****',2,4);
        case 'bank_card':
            return substr($data,0,4).'***'.substr($data,-4);
        case 'alipay_account':
            return substr($data,0,3).'***'.substr($data,-4);
        default:
            return $data;
        }
    }

    //格式化日期时间
    static function format_time($time,$mode='Y年n月j日'){
        switch($mode) {
        case 'Y-m-d':
        case 'Y-n-j':
        case 'Y年n月j日':
        case 'y年n月j日 G点':
            return date($mode,strtotime($time));
        default:
            return $time;
        }
    }

    //格式数字
    static function format_number($number,$type='bank_card'){
        switch($type) {
        case 'money':
            return number_format($number,2,'.',',');
        case 'bank_card':
            return chunk_split($number,4,' ');
        case 'phone':
            $phone=array(substr($number,0,3),substr($number,3,4),substr($number,7,4));
            return implode(' ',$phone);
        case 'id_card':
            if (strlen($number) === 15) {
                $id_card=array(substr($number,0,6),substr($number,6,2),substr($number,8,4),substr($number,12));
            } else {
                $id_card=array(substr($number,0,6),substr($number,6,4),substr($number,10,4),substr($number,14));
            }
            return implode(' ',$id_card);
        default:
            return $number;
        }
    }

    //智能时间
    static function friendly_time($time){
        $stamp=strtotime($time);
        $s_time=time()-$stamp;
        if ($s_time <= 60) return '刚刚';
        if ($s_time <= 3600) return intval($s_time/60).'分钟前';

        list($year,$data,$day,$week,$apm,$clock)=explode('|',date('Y|Y-m-d|n月j日|w|a|g:i',$stamp));

        $apm=$apm === 'am' ? '上午' : '下午';
        if ($data === date('Y-m-d')) return $apm.' '.$clock;
        if ($data === date("Y-m-d",strtotime("-1 day"))) return '昨天 '.$apm.' '.$clock;

        $option=array('天','一','二','三','四','五','六');
        if ($s_time <= 604800) return implode(' ',array('星期'.$option[$week],$apm,$clock));

        if ($year === date('Y')) return implode(' ',array($day,$apm,$clock));
        return $time;
    }



}