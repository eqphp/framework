<?php

class a_demo{

    //静态类
    private $static_class;


    //列表
    static function index(){
        with('view')->assign('name', 'art')->display('demo/index');
    }

    //详情
    static function detail(){
    
    }

    //创建
    static function create(){

    }


    //修改
    static function modify(){

    }

    //删除
    static function delete(){

    }


}