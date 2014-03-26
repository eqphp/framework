<?php
class a_upload{

private $static_class;

static function index(){
$tpl=smarty();

$head['title']='EQPHP 文件上传(file upload)';
$tpl->assign('head',$head);

$lie=1;
$param['type']=rq($lie);
$param['dir']=rq($lie+1);
$param['id']=rq($lie+2);
$tpl->assign('up',$param);
$tpl->display('plugin/upload');
}


//站内上传
static function upload(){

$basic_css='<link rel="stylesheet" type="text/css" href="'.dc_url_style.'basic.css" />';
http::out($basic_css,true,false);

$error=config('error','upload');

$user_id=8; //session::get('id');
if (!$user_id) http::out($error[301]);

$cf_type=config('self_type','upload');
$cf_dir=config('self_dir','upload');

$dir=post('up_dir','int');
if (!$dir) http::out($error[302]);

$save_path=dc_file_file.$cf_dir[$dir];
if (!is_dir($save_path)) http::out($error[304]);

//动态创建上传目录
$auto_create_dir=''; //date('Y_m').'/'
// $save_path.=$auto_create_dir;
// if (!file_exists($save_path)) mkdir($save_path);
if (!is_writable($save_path)) http::out($error[305]);

$up_info=self::get('file_name');
if ($up_info===null) return http::out($error[303]);
if ($up_info===false) http::out($error[306]);

$max_size=config('max_size|4','upload')*1048576;

if ($up_info['size']>$max_size) http::out($error[308]);
if ($up_info['error']==1) http::out($error[309]);
if ($up_info['error']==2) http::out($error[307]);

$ext_arr=array(
'pic'=>explode(',',$cf_type['pic']),
'media'=>explode(',',$cf_type['media']),
'doc'=>explode(',',$cf_type['doc']),
'html'=>explode(',',$cf_type['html']),
'zip'=>explode(',',$cf_type['zip'])
);

$type=post('up_type','title');
if (!$type || !in_array($type,array_keys($ext_arr))) http::out($error[304]);
if (!in_array(trim($up_info['type'],'.'),$ext_arr[$type])) http::out($error[310]);

//$info_id=post('up_id','int'); //信息ID
$file_name=$user_id.'_'.date("ymdHi").'_'.rand(1000, 9999).$up_info['type'];

$file_path=$save_path.$file_name;
if (!move_uploaded_file($up_info['name'],$file_path)) http::out($error[311]);
chmod($file_path,0644);

//处理上传日志或入库逻辑

http::out(dc_url_file.$cf_dir[$dir].$auto_create_dir.$file_name);
}


//编辑器上传
static function kind_edit(){

$error=config('error','upload');

$user_id=8; //session::get('id')
if (!$user_id) self::kd_json(301);

if (!is_dir(dc_file_pic)) self::kd_json(304);
if (!is_writable(dc_file_pic)) self::kd_json(305);

$up_info=self::get('imgFile');
if (!$up_info || $up_info=='upload error') self::kd_json(306);

$max_size=config('max_size|3','upload')*104857600;
if ($up_info['size']>$max_size) self::kd_json(308);

$config_type=config('edit_type','upload');

$rq_type=isset($_GET['dir']) ? strval($_GET['dir']) : 'pic';
$config_ext=$config_type['pic'];
if (in_array($rq_type,array_keys($config_type))) {
$config_ext=$config_type[$rq_type];
}

$ext_arr=explode(',',$config_ext);
if (!in_array(trim($up_info['type'],'.'),$ext_arr)) {
http::json(array('error'=>1,'message'=>$error[310].',允许{'.$config_ext.'}'));
}

$save_path=dc_file_editor.$rq_type.'/';
$save_url=dc_url_editor.$rq_type.'/';
if ($rq_type=='pic') {
$save_path.=date("ym").'/';
$save_url.=date("ym").'/';
}

if (!file_exists($save_path)) mkdir($save_path);

$file_name=$user_id.'_'.date("dHi").'_'.rand(1000,9999).$up_info['type'];
$file_path=$save_path.$file_name;
if (!move_uploaded_file($up_info['name'],$file_path)) self::kd_json(311);
chmod($file_path,0644);

//上传日志或入库逻辑

http::json(array('error'=>0,'url'=>$save_url.$file_name));
}


//处理kind_edit方法输出json
private static function kd_json($error_no){
http::json(array('error'=>1,'message'=>$error[$error_no]));
}

//获取上传文件信息
private static function get($input_name='file_name'){
if (!$_FILES) return false;
$input_file=$_FILES[$input_name];
if (is_uploaded_file($input_file['tmp_name'])) {
preg_match('|\.(\w+)$|',$input_file['name'],$suffix);
return array(
'size'=>$input_file['size'],
'type'=>strtolower($suffix[0]),
'file'=>$input_file['name'],
'name'=>$input_file['tmp_name'],
'error'=>$input_file['error']);
}
}

//编辑图像
static function edit(){
$img_name=post('img_name','title');
list($width,$height)=getimagesize($img_name);

$p_width=post('p_w','int');
$p_height=post('p_h','int');

//先缩放
$res=imagecreatetruecolor($p_width,$p_height);
$img=img::open($img_name);
imagecopyresampled($res,$img,0,0,0,0,$p_width,$p_height,$width,$height);
img::clear($img);

//再裁切
$new_img=imagecreatetruecolor(post('n_w','int'),post('n_h','int'));
imagecopyresampled($new_img,$res,0,0,post('t_x','int'),post('t_y','int'),$p_width,$p_height,$p_width,$p_height);
$img_name='8_'.date('ymd').'_'.rand(100,999).'.gif';
img::save($new_img,$img_name,dc_file_create);
http::json(array('error'=>0,'info'=>$img_name),true);
}


}