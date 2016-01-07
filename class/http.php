<?php
//rely on: html help logger smarty
class http{

    //域内跳转
    static function redirect($url=null){
        header('Location: '.U_R_L.$url);
        exit;
    }

    //异常终止并跳转
    static function abort($abort_message='',$redirect_url='',$wait_time=10){
        $data=compact('abort_message','redirect_url','wait_time');
        //smarty()->assign($data)->display('abort/fail');
        exit;
    }
	
    //完成、结束并终止并跳转
    static function success($tip_message='',$redirect_url='',$wait_time=10){
        $data=compact('tip_message','redirect_url','wait_time');
        //smarty()->assign($data)->display('abort/success');
        exit;
    }

    //输出script
    static function script($data=null,$type='back_refresh',$is_exit=true){
        $script=html::script($data,$type);
        $is_exit && exit($script);
        echo $script;
    }

    //输出json
    static function json($data,$is_end=true){
        header('Content-Type:application/json; charset=utf-8');
        $json=json_encode($data);
        $is_end && exit($json);
        echo $json;
    }

    //输出xml
    static function xml($data,$root='rss',$is_end=true){
        header('Content-Type:text/xml; charset=utf-8');
        $xml='<?xml version="1.0" encoding="utf-8"?>';
        $xml.="<$root>".help::data_xml($data)."</$root>";
        $is_end && exit($xml);
        echo $xml;
    }

    //构造post提交并获取接口返回数据
    //header: array('Cookie: '.http_build_query($_COOKIE,'','; '))
    static function curl($url,$data=null,$header=null){
        $ch=curl_init();

        if (is_array($url)) {
            $url=$url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'];
        }

        if ($data) {
            if (is_array($data)) {
				$data=http_build_query($data);			
			}
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,trim($data));
        }

        if ($header) {
            $header[]='Content-type: application/x-www-form-urlencoded';
            curl_setopt($ch,CURLOPT_HEADER,1);
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }

        curl_setopt($ch,CURLOPT_URL,$url);
        if (defined('CURLOPT_SSL_VERIFYPEEP')) {
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEEP,0);
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_TIMEOUT,10);

        $result=curl_exec($ch);
        $status=curl_getinfo($ch);
        if (curl_errno($ch)) {
            logger::exception('curl',curl_errno($ch).curl_error($ch));
        }

        $result=substr($result,$status['header_size']);
        curl_close($ch);
        return json_decode($result,true);
    }

    //以post请求发送socket并返回接口数据
    static function socket($url,$data){
        //解析url
        if (!is_array($url)) {
            $url=parse_url($url);
        }

        if (!isset($url["port"])) {
            $url['port']=80;
        }

        //打开socket
        $fp=fsockopen($url['host'],$url['port'],$error_no,$error_info,30);
        if (!$fp) {
            $info='error:('.$error_no.')'.$error_info;
            logger::exception('socket',$info);
            throw new Exception($info,107);
        }

        //组装发送数据
        if (is_array($data)) {
            $data=http_build_query($data);
        }
        $data=trim($data);

        //构造头部信息
        $head='POST '.$url['path']." HTTP/1.0\r\n";
        $head.='Host: '.$url['host']."\r\n";
        $head.='Referer: http://'.$url['host'].$url['path']."\r\n";
        $head.="Content-type: application/x-www-form-urlencoded\r\n";
        $head.='Content-Length: '.strlen($data)."\r\n\r\n";
        $head.=$data;

        //接收并返回结果
        fputs($fp,$head);
        $info='';
        while (!feof($fp)) {
            $info=fgets($fp);
        }
        return json_decode($info,true);
    }

    //发送http错误头信息
    static function send($code=404,$is_end=true){
        $status=config(null,'http_status');
        if (isset($status[$code])) {
            header('HTTP/1.1 '.$code.' '.$status[$code]);
            header('Status:'.$code.' '.$status[$code]);
	    echo $status[$code];
            //smarty()->display('abort/'.$code);
            $is_end && exit;
        }
    }

    //下载文件
    static function download($data,$save_name,$is_path=false){
        $extension=preg_replace('/.*\./','',$save_name);
        $mime_type=config($extension,'mime_type');
        empty($mime_type) and $mime_type='application/octet-stream';
        $file_name=help::process_download_file_name($save_name);
        if ($is_path) {
            if (is_file($data) && ($fp=fopen($data,'rb')) !== false) {
                self::send_download_file_header($mime_type,filesize($data),$file_name);
		if (ob_get_level() !== 0 && ob_end_clean() === false) {
			ob_clean();
		}
                while (!feof($fp) && ($source=fread($fp,1048576)) !== false) {
                    echo $source;
                }
                fclose($fp);
                exit;
            }
            throw new Exception('read download file fail',108);
        }
        self::send_download_file_header($mime_type,strlen($data),$file_name);
        exit($data);
    }

    //发送下载文件头信息
    static function send_download_file_header($mime_type,$file_size,$file_name){
	header('Content-Type: '.$mime_type);
	header('Content-Disposition: attachment; '.$file_name);
	header('Expires: 0');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.$file_size);
	header('Cache-Control: private, no-transform, no-store, must-revalidate');
    }

    //判断是否异步请求
    static function is_ajax(){
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $x_requested=strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
            if ($x_requested === 'xmlhttprequest') {
                return true;
            }
        }
        return false;
    }

	//判断是否SSL协议
    function is_ssl(){
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] === '1' || strtolower($_SERVER['HTTPS']) === 'on') {
                return true;
            }
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            if (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
                return true;
            }
        }
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443') {
            return true;
        }
        return false;
    }

}