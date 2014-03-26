<?php
class s_pic{

    //返回验证码图片
    static function code($code='eqphp',$width=65,$height=26,$size=15){
        //设置head信息，输出图片
        header("Content-Type:image/gif");

        //设置session
        if (isset($_GET['check'])) {
            session::set(form::get('check','name'),$code);
        }

        $img=img::create($width,$height,'ffffff');
        $arc_color=img::color($img,'a50000');
        $pix_color=img::color($img,'bbe6f7');
        $font_color=img::color($img,'29a3ee');

        for ($i=0; $i < 100; $i++) { //绘模糊作用的点，每次1px
            imagesetpixel($img,rand(0,$width),rand(0,$height),$pix_color);
        }

        imagettftext($img,$size,rand(-5,5),8,18,$font_color,dc_data_static.'font/lucon.ttf',$code);
        imagerectangle($img,0,0,$width-1,$height-1,$font_color);
        imagegif($img);
        imagedestroy($img);
    }


    //添加水印
    static function mark($picture,$mark=array('http://www.eqphp.com','艺青科技'),$filename=array('test.png','file/create/')){
        $image=img::open($picture);
        $img_width=img::info($image,'width');
        $img_height=img::info($image,'height');
        $text_color=img::color($image,'fefefe');
        $alpha=imagecolorallocatealpha($image,15,15,15,85);
        $border_color=img::color($image,'efefef');
        img::draw($image,$border_color,array(0,0,$img_width-1,$img_height-1),'rectangle');
        img::fill($image,$alpha,array(1,$img_height-35,$img_width-2,$img_height-2),'rectangle');
        imagettftext($image,9,0,$img_width-145,$img_height-14,$text_color,dc_data_static.'font/lucon.ttf',$mark[0]);
        imagettftext($image,9,0,$img_width-210,$img_height-15,$text_color,dc_data_static.'font/msyh.ttf',$mark[1]);
        return img::save($image,$filename[0],$filename[1]);
    }

    //按照指定尺寸缩放图片
    static function zoom($picture,$filename=array('eq80_test.png','file/create/'),$length,$is_height=false){
        $image=img::open($picture);
        $start_width=img::info($image,'width');
        $start_height=img::info($image,'height');

        $end_width=$length;
        $end_height=round(($start_height*$end_width)/$start_width);
        if ($is_height) {
            $end_width=round(($start_width*$length)/$start_height);
            $end_height=$length;
        }

        $canvas=img::create($end_width,$end_height,'ffffff',true);
        $param=array(0,0,0,0,$end_width,$end_height,$start_width,$start_height);
        img::copy($canvas,$image,4,$param);
        img::save($canvas,$filename[0],$filename[1]);

        return $filename[1].$filename[0];
    }

    //生成指定尺寸的用户头像
    static function avatar($picture,$size_data=array(200,100,75,50,32),$save_dir=''){
        $image=img::open($picture);
        $true_size=img::info($image,'width');
        $save_name=basename($picture,pathinfo($picture,PATHINFO_EXTENSION));

        if ($true_size >= $size_data[0]) {
            $arr=null;
            foreach ($size_data as $target_size) {
                $target_img=img::create($target_size,$target_size,'ffffff',true);
                $arr=array(0,0,0,0,$target_size,$target_size,$true_size,$true_size);
                $copy_result=img::copy($target_img,$image,5,$arr);
                if ($copy_result) {
                    img::save($target_img,$save_name.'.gif',$save_dir.$target_size.'/');
                }
            }
            return true;
        }
    }

    //输出提示
    static function tip(){
        $info='1、验证码：check_code($code="eqphp",$width=65,$height=26,$size=24)<br>';
        $info.='2、添加水印：mark($img,$mark_info=array("http://www.eq80.com","艺青科技"),$save_arr=array("eq80_test.png","file/create/"))<br>';
        $info.='3、图片缩放：zoom($img,$save_arr=array("eq80_test.png","file/create/"),$length,$is_height=false)<br>';
        $info.='4、产生头像：avatar($img,$size_arr=array(100,75,50,32),$save_dir="file/pic/avatar/")</font><br><br>';
        return $info;
    }


}