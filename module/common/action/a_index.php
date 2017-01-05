<?php

class a_index{

    //静态类(yes)
    private $static_class;

    //首页
    static function index(){
        a_command::create_directory();
        a_command::modify_privilege();

        http::cookie('framework_name', 'EQPHP');
        $data = array('title' => 'EQPHP Framework 3.0', 'url' => U_R_L);
        return with('view')->assign($data)->display('index.html');
    }


}