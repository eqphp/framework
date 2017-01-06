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
        with('view')->assign('name', 'art')->display('demo/demo');
    }

    //创建
    static function create(){

        out('lol');

    }


    //修改
    static function modify(){

    }

    //删除
    static function delete(){

    }


}