<?php

class a_captcha{

	private $static_class;

    #获取图片验证码
    static function index(){
        $captcha=basic::code(5);
        session::set('captcha',$captcha);
        s_picture::code($captcha,90,44);
    }



}