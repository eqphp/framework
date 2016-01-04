<?php
class a_editor extends a_auth{

    //编辑器上传
    function upload(){
        if (isset($_FILES['imgFile'])) {
            try{
                $save_name=$this->user_id.'_'.date("dHis");
                $upload=new s_upload($_FILES['imgFile'],$save_name,5,'editor',date('ym').'/');
                $file_name=$upload->get('file_name');
                http::json(array('error'=>0,'url'=>U_R_L.strstr($file_name,'file/editor/')));
            } catch (sException $e) {
                $message=config('error.'.$e->error,'upload');
                http::json(array('error'=>1,'message'=>$message));
            }
        }
        http::json(array('error'=>1,'message'=>'system busy, please try again later'));
    }

}