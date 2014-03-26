<?php
class img{

    // point:点;line:线;arc:弧;fill:填充;border:边
    // ellipse:椭圆;rectangle:矩形;polygon:多边形

    //创建
    static function create($width,$height,$bgcolor=false,$type=false){
        $create=$type ? 'imagecreatetruecolor' : 'imagecreate';
        $image=$create($width,$height);
        $bgcolor && self::color($image,$bgcolor);
        return $image;
    }


    //打开
    static function open($filename){
        $format=strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        $create=($format === 'jpg') ? 'imagecreatefromjpeg' : 'imagecreatefrom'.$format;
        if (in_array($format,array('gif','jpeg','png','wbmp','xbm','xmp'))) {
            return $create($filename);
        }
    }


    //设置颜色
    static function color(&$imgage,$color='ffffff',$delete=false){
        $cd=str_split($color,((strlen($color) > 4) ? 2 : 1));
        $color=imagecolorallocate($imgage,hexdec($cd[0]),hexdec($cd[1]),hexdec($cd[2]));
        $delete && imagecolordeallocate($imgage,$color);
        return $color;
    }


    //绘制图形
    static function draw(&$image,$color,$param,$name="line"){

        //像素点
        if ($name == 'point') {
            return imagesetpixel($image,$param[0],$param[1],$color);
        }

        //线(起点/终点)、椭圆(中心点/宽度-高度)、矩形(左顶点/右底点)
        if ($name == 'line' || $name == 'ellipse' || $name == 'rectangle') {
            $draw='image'.$name;
            return $draw($image,$param[0],$param[1],$param[2],$param[3],$color);
        }

        //弧(中心点/宽度-高度/起始角度-结束角度(0-360))
        if ($name == 'arc') {
            return imagearc($image,$param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$color);
        }

        //多边形$param:各顶点坐标(一维数组),顶点数
        if ($name == 'polygon') {
            return imagepolygon($image,$param,count($param)/2,$color);
        }

    }

    //填充颜色
    static function fill(&$image,$color,$param,$name="fill"){

        //填充
        if ($name == 'fill') {
            return imagefill($image,$param[0],$param[1],$color);
        }

        //弧形填充
        if ($name == 'arc') {
            return imagefilledarc($image,$param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$color,$param[6]);
        }

        //填充椭圆、矩形
        if ($name == 'ellipse' || $name == 'rectangle') {
            $fill='imagefilled'.$name;
            return $fill($image,$param[0],$param[1],$param[2],$param[3],$color);
        }

        //填充多边形
        if ($name == 'polygon') {
            return imagefilledpolygon($image,$param,count($param)/2,$color);
        }

        //区域填充到指定颜色的边界为止
        if ($name == 'border') {
            return imagefilltoborder($image,$param[0],$param[1],$param[2],$color);
        }

    }


    //创建文本
    static function text(&$image,$text,$font,$color,$mode=2,$p_x=0,$p_y=0){
        $param=array(1=>'char',2=>'string',3=>'charup',4=>'stringup');
        $input='image'.(is_numeric($mode) ? $param[$mode] : $mode);
        return $input($image,$font,$p_x,$p_y,$text,$color);
    }


    //旋转
    static function rotate(&$image,$angle,$color,$alpha=0){
        return imagerotate($image,$angle,$color,$alpha);
    }


    //复制图像
    static function copy(&$image,$picture,$mode,$param){
        if ($mode == 1) { //拷贝
            return imagecopy($image,$picture,$param[0],$param[1],$param[2],$param[3],$param[4],$param[5]);
        }
        if ($mode == 2) { //拷贝+合并
            return imagecopymerge($image,$picture,$param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6]);
        }
        if ($mode == 3) { //灰度拷贝+合并
            return imagecopymergegray($image,$picture,$param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6]);
        }
        if ($mode == 4) { //拷贝+调整大小
            return imagecopyresized($image,$picture,$param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7]);
        }
        if ($mode == 5) { //采样+拷贝+调整大小
            return imagecopyresampled($image,$picture,$param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7]);
        }
    }


    //设置样式、风格
    static function set(&$image,$value,$method){
        if ($method == 'border') { //画线粗细
            return imagesetthickness($image,(int)($value));
        }
        if ($method == 'style') { //画线风格
            return imagesetstyle($image,(array)($value));
        }
        if ($method == 'brush') { //画笔图像
            return imagesetbrush($image,$value);
        }
        if ($method == 'pattern') { //填充的贴图 图案
            return imagesettile($image,$value);
        }
        if ($method == 'alias') { //抗锯齿
            return imageantialias($image,(bool)($value));
        }
        if ($method == 'alpha') { //alpha混色标志
            return imagelayereffect($image,(int)($value));
        }
        if ($method == 'transparent') { //透明色
            return imagecolortransparent($image,(int)($value));
        }
        if ($method == 'mix') { //混色模式
            return imagealphablending($image,(bool)($value));
        }
    }


    //添加滤镜
    static function filter(&$image,$type=IMG_FILTER_GRAYSCALE,$arg1=0,$arg2=0,$arg3=0){
        return imagefilter($image,$type,$arg1,$arg2,$arg3);
    }


    //保存图像
    static function save(&$image,$filename='test.png',$dir="file/create/"){
        $format=strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        $save=($format == 'jpg') ? 'imagejpeg' : 'image'.$format;
        $save($image,$dir.$filename);
        imagedestroy($image);
        return $dir.$filename;
    }


    //输出图像
    static function out($image,$format='gif'){
        $out='image'.$format;
        return $out($image);
    }


    //销毁图像
    static function clear(&$image){
        return imagedestroy($image);
    }


    //获取图像相关信息
    static function info($image,$option){
        if ($option == 'width') return imagesx($image);
        if ($option == 'height') return imagesy($image);
        if ($option == 'text_width') return imagefontheight($image);
        if ($option == 'text_height') return imagefontwidth($image);
        return array(gd_info(),imagetypes());
    }


    //查询img类方法
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、穿件画布：create($width=120,$height=150,$bgcolor=false,$type=false)<br>';
        $info.='2、打开图像：open($filename)<br>';
        $info.='3、设置颜色：color(&$image,$color="ffffff",$delete=false)<br>';
        $info.='4、绘画图形：draw(&$image,$color,$param,$name="line")<br>';
        $info.='5、填充颜色：fill(&$image,$color,$param,$name="fill")<br>';
        $info.='6、输入文本：text(&$image,$text,$font,$color,$mode=2,$p_x=0,$p_y=0)<br>';
        $info.='7、旋转图像：rotate(&$image,$angle,$color,$alpha=0)<br>';
        $info.='8、复制图像：copy(&$image,$picture,$mode,$param)<br>';
        $info.='9、风格样式：set(&$image,$value,$method)<br>';
        $info.='10、图片过滤：filter(&$image,$type=IMG_FILTER_GRAYSCALE,$arg1=0,$arg2=0,$arg3=0)<br>';
        $info.='11、保存图像：save(&$image,$filename="test.png",$dir="file/create/")<br>';
        $info.='12、输出图片：out(&$image,$format="gif")<br>';
        $info.='13、清理资源：clear(&$image)<br>';
        $info.='14、获取信息：info($image,$option)</font><br><br>';
        return $info;
    }


}