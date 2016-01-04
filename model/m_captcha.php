<?php

class m_captcha{

    const TABLE_CAPTCHA='captcha';


    static function record($type,$phone,$captcha,$user_id=0){
        $data=compact('type','phone','captcha','user_id');
        return db::post(self::TABLE_CAPTCHA,array_filter($data));
    }


    static function get_send_times($type,$user_id,$cycle='-4 hours'){
        $time=array('egt',date('Y-m-d H:i:s',strtotime($cycle)));
        $condition=compact('user_id','type','time');
        return db::field(self::TABLE_CAPTCHA,'count(1)',$condition);
    }













}