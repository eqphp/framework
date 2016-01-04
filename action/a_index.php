<?php

class a_index{

    //静态类(yes)
    private $static_class;

	//首页
    static function index(){
        $head=array('title'=>'EQPHP开源中文WEB应用开发框架');
        input::cookie('frame_name','EQPHP');

        $logo_file=DATA_STORE.'txt/logo_pic.txt';
        $source=base64_decode(file_get_contents($logo_file));
        file_put_contents(FILE_CREATE.'eqphp_logo.png',$source);
        $logo='<img src="'.URL_CREATE.'eqphp_logo.png">';
        smarty()->assign(compact('head','logo'))->display('index');
   }
   
}