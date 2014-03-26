<?php
class img{

public static $element=array('setpixel'=>'点','line'=>'线',
'arc'=>'弧','ellipse'=>'椭圆','rectangle'=>'矩形','polygon'=>'多边形',
'fill'=>'填充','border'=>'边');

//创建
static function create($width=120,$height=150,$bg_color=false,$type=false){
$act='imagecreate'.($type?'truecolor':null);
$res=$act($width,$height);
if ($bg_color) { self::color($res,$bg_color); }
return $res;
}

//打开
static function open($file_name){
$ext_arr=explode('.',$file_name);
$format=end($ext_arr);
$format=($format=='jpg')?'jpeg':$format;
if (in_array($format,array('gif','jpeg','png','wbmp','xbm','xmp'))) {
$act='imagecreatefrom'.$format;
return $act($file_name);
} else {
return 'format error';
}
}

//设置颜色
static function color($res,$color='ffffff',$del=false){
$cr=str_split($color,((strlen($color)>4)?2:1));
$color=imagecolorallocate($res,hexdec($cr[0]),hexdec($cr[1]),hexdec($cr[2]));
if ($del) { imagecolordeallocate($res,$color); }
return $color;
}

//绘制
static function draw($res,$color,$arr,$name="line"){
$key=array_keys(self::$element);
$act='image'.$name;
if ($name==$key[0]) {//像素点
return $act($res,$arr[0],$arr[1],$color);
}
if ($name==$key[1] || $name==$key[3] || $name==$key[4]) {
return $act($res,$arr[0],$arr[1],$arr[2],$arr[3],$color);
}
if ($name==$key[2]) {//弧
return $act($res,$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$color);
}
if ($name==$key[5]) {//多边形
return $act($res,$arr,end($arr),$color);
}
return 'method error';
}

//填充
static function fill($res,$color,$arr,$name="fill"){
$key=array_keys(self::$element);
if ($name==$key[6]) {
return imagefill($res,$arr[0],$arr[1],$color);
}
if ($name==$key[2]) {
return imagefilledarc($res,$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$color,$arr[6]);
}
if ($name==$key[3] || $name==$key[4]) {
$act='imagefilled'.$name;
return $act($res,$arr[0],$arr[1],$arr[2],$arr[3],$color);
}
if ($name==$key[5]) {
return imagefilledpolygon($res,$arr,end($arr),$color);
}
if ($name==$key[7]) {
return imagefilltoborder($res,$arr[0],$arr[1],end($arr),$color);
}
return 'method error';
}

//文本
static function text($res,$str,$font,$color,$mode=2,$p_x=0,$p_y=0){
$arr=array(1=>'char',2=>'string',3=>'charup',4=>'stringup');
$act='image'.(is_numeric($mode)?$arr[$mode]:$mode);
return $act($res,$font,$p_x,$p_y,$str,$color); 
}

//旋转
static function rotate($res,$angle,$color,$alpha=0){
return imagerotate($res,$angle,$color,$alpha);
}

//复制
static function copy($res,$res_img,$mode,$arr){
if ($mode==1) {//拷贝
return imagecopy($res,$res_img,$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5]);
}
if ($mode==2) {//拷贝+合并
return imagecopymerge($res,$res_img,$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6]);
}
if ($mode==3) {//灰度拷贝+合并
return imagecopymergegray($res,$res_img,$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6]);
}
if ($mode==4) {//拷贝+调整大小
return imagecopyresized($res,$res_img,$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$arr[7]);
}
if ($mode==5) {//采样+拷贝+调整大小
return imagecopyresampled($res,$res_img,$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$arr[7]);
}
}

//设置样式、风格
static function set($res,$method,$mix){
if ($method==1) {//画线粗细
return imagesetthickness($res,(int)($mix));
}
if ($method==2) {//画线风格
return imagesetstyle($res,(array)($mix));
}
if ($method==3) {//画笔图像
return imagesetbrush($res,$mix);
}
if ($method==4) {//填充的贴图
return imagesettile($res,$mix);
}
if ($method==5) {//抗锯齿
return imageantialias($res,(bool)($mix));
}
if ($method==6) {//alpha混色标志
return imagelayereffect($res,(int)($mix));
}
if ($method==7) {//透明色
return imagecolortransparent($res,(int)($mix));
}
if ($method==8) {//混色模式
return imagealphablending($res,(bool)($mix));
}
return 'method error';
}

//滤镜
static function filter($res,$type=IMG_FILTER_GRAYSCALE,$arg1=0,$arg2=0,$arg3=0){
return imagefilter($res,$type,$arg1,$arg2,$arg3);
}

//保存
static function save($res,$name='eq80_gd_act.gif',$dir="file/create/"){
$ext_arr=explode('.',$name);
$ext_name=strtolower(end($ext_arr));
if ($ext_name=='jpg') $ext_name='jpeg';
$act='image'.$ext_name;
$act($res,$dir.$name);
self::clear($res);
return $dir.$name;
}

//输出
static function out($res,$format='gif'){
$act='image'.$format;
return $act($res);
}

//销毁
static function clear($res){
return imagedestroy($res);
}

//获取相关信息
static function info($res,$option){
if ($option=='width') return imagesx($res);
if ($option=='height') return imagesy($res);
if ($option=='test_width') return imagefontheight($font);
if ($option=='test_height') return imagefontwidth($font);
return array(gd_info(),imagetypes());
}

//查询img类方法
static function tip(){
$info='<br><font color="green">';
$info.='1、穿件画布：create($width=120,$height=150,$bg_color=false,$type=false)<br>';
$info.='2、打开图像：open($file_name)<br>';
$info.='3、设置颜色：color($res,$color="ffffff",$del=false)<br>';
$info.='4、绘画图形：draw($res,$color,$arr,$name="line")<br>';
$info.='5、填充颜色：fill($res,$color,$arr,$name="fill")<br>';
$info.='6、输入文本：text($res,$str,$font,$color,$mode=2,$p_x=0,$p_y=0)<br>';
$info.='7、旋转图像：rotate($res,$angle,$color,$alpha=0)<br>';
$info.='8、复制图像：copy($res,$res_img,$mode,$arr)<br>';
$info.='9、风格样式：set($res,$method,$mix)<br>';
$info.='10、图片过滤：filter($res,$type=IMG_FILTER_GRAYSCALE,$arg1=0,$arg2=0,$arg3=0)<br>';
$info.='11、保存图像：save($res,$name="eq80_gd_act.gif",$dir="file/create/")<br>';
$info.='12、输出图片：out($res,$format="gif")<br>';
$info.='13、清理资源：clear($res)<br>';
$info.='14、获取信息：info($res,$option)</font><br><br>';
return $info;
}


}