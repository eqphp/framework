<?php

class a_index{

    //静态类(yes)
    private $static_class;

	//首页
    static function index(){
	$url=U_R_L;
        $head=array('title'=>'EQPHP开源中文WEB应用开发框架');
        input::cookie('frame_name','EQPHP');

        $logo_file=DATA_STORE.'txt/logo_pic.txt';
        $source=base64_decode(file_get_contents($logo_file));
        file_put_contents(FILE_TEMP.'eqphp_logo.png',$source);
        $logo='<img src="'.URL_TEMP.'eqphp_logo.png">';
	$data=compact('url','head','logo');
	return with('view')->assign($data)->display('index.html');
   }
   
}