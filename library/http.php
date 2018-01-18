<?php

//rely on: system basic file html help logger
class http{

    //域内跳转
    static function redirect($url = null){
        header('Location: ' . U_R_L . $url);
        exit();
    }

    //异常终止并跳转
    static function abort($abort_message = '', $redirect_url = '', $wait_time = 10){
        $data = compact('abort_message', 'redirect_url', 'wait_time');
        basic::with(new view())->assign($data)->display('abort/fail');
        exit();
    }

    //完成、结束并终止并跳转
    static function success($tip_message = '', $redirect_url = '', $wait_time = 10){
        $data = compact('tip_message', 'redirect_url', 'wait_time');
        basic::with(new view())->assign($data)->display('abort/success');
        exit();
    }

    //输出script
    static function script($data = null, $type = 'back_refresh', $is_exit = true){
        $script = html::script($data, $type);
        $is_exit && exit($script);
        echo $script;
    }

    //输出json
    static function json($data, $is_exit = true){
        headers_sent() or header('Content-Type:application/json; charset=utf-8');
        $json = json_encode($data);
        $is_exit && exit($json);
        echo $json;
    }

    //输出xml
    static function xml($data, $root = 'rss', $is_exit = true){
        headers_sent() or header('Content-Type:text/xml; charset=utf-8');
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= "<{$root}>" . help::data_xml($data) . "</{$root}>";
        $is_exit && exit($xml);
        echo $xml;
    }

    //COOKIE设置
    static function cookie(){
        $param = func_get_args();
        list($key, $value) = $param;
        $expire = isset($param[2]) ? $param[2] : 2592000;
        $expire += time();

        $domain = system::config('system.domain.cookie');
        list($path, $secure, $only) = array('/', false, false);
        return setCookie($key, $value, $expire, $path, $domain, $secure, $only);
    }

    //构造post提交并获取接口返回数据
    //header: array('Cookie: '.http_build_query($_COOKIE,'','; '))
    static function curl($url, $data = null, $option = array(), $header = null){
        $option += array('request_type' => 'json', 'response_type' => 'json', 'is_array' => true, 'xml_tag' => 'item');
        if (is_array($url)) {
            $url = $url['scheme'] . '://' . $url['host'] . ':' . $url['port'] . $url['path'];
        }
        $content_type = array(
            'xml' => 'Content-Type: text/xml',
            'json' => 'Content-Type: application/json',
            'form' => 'Content-type: application/x-www-form-urlencoded',
        );
        if (isset($content_type[$option['request_type']])) {
            $header[] = $content_type[$option['request_type']] . '; charset=utf-8';
        }
        if (is_array($data) && $data) {
            if ($option['request_type'] === 'json') {
                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            } elseif ($option['request_type'] === 'xml') {
                $data = help::data_xml($data);
            } else {
                $data = urldecode(http_build_query($data));
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (defined('CURLOPT_SSL_VERIFYPEEP')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEEP, 0);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if ($data) {
            if (isset($option['request_method'])) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($option['request_method']));
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, trim($data));
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            logger::exception('curl', curl_errno($ch) . ': ' . curl_error($ch));
        }

        $content_length = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $response = trim(substr($response, $content_length));
        curl_close($ch);

        if ($option['response_type'] === 'json') {
            return json_decode($response, $option['is_array']);
        } elseif ($option['response_type'] === 'xml') {
            return simplexml_load_string($response);
        }
        return $response;
    }

    //以post请求发送socket并返回接口数据
    static function socket($url, $data){
        //解析url
        if (!is_array($url)) {
            $url = parse_url($url);
        }

        if (!isset($url['port'])) {
            $url['port'] = 80;
        }

        //打开socket
        $fp = fsockopen($url['host'], $url['port'], $error_no, $error_info, 30);
        if (!$fp) {
            $message = 'error:(' . $error_no . ')' . $error_info;
            logger::exception('socket', $message);
            throw new Exception($message, 107);
        }

        //组装发送数据
        if (is_array($data)) {
            $data = http_build_query($data);
        }
        $data = trim($data);

        //构造头部信息
        $head = 'POST ' . $url['path'] . ' HTTP/1.0' . PHP_EOL;
        $head .= 'Host: ' . $url['host'] . PHP_EOL;
        $head .= 'Referer: http://' . $url['host'] . $url['path'] . PHP_EOL;
        $head .= 'Content-type: application/x-www-form-urlencoded' . PHP_EOL;
        $head .= 'Content-Length: ' . strlen($data) . PHP_EOL . PHP_EOL;
        $head .= $data;

        //接收并返回结果
        fputs($fp, $head);
        $content = '';
        while (!feof($fp)) {
            $content = fgets($fp);
        }
        return json_decode($content, true);
    }

    //发送http错误头信息
    static function send($code = 404, $is_out = true, $is_exit = true){
        $message = basic::meta('http_status.' . $code);
        if (strlen($message)) {
            header('HTTP/1.1 ' . $code . ' ' . $message);
            header('Status:' . $code . ' ' . $message);
            if ($is_out) {
                smarty()->display('abort/' . $code);
            }
            $is_exit && exit();
        }
    }

    //下载文件
    static function download($data, $save_name, $is_path = false){
        $extension = preg_replace('/.*\./', '', $save_name);
        $mime_type = basic::meta('mime_type.' . $extension);
        empty($mime_type) and $mime_type = 'application/octet-stream';
        $process_file_name = function () use ($save_name){
            $save_name = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|', ','), '', $save_name);
            if (isset($_SERVER['HTTP_USER_AGENT']) && ($user_agent = $_SERVER['HTTP_USER_AGENT'])) {
                if (preg_match('/MSIE/', $user_agent)) {
                    return 'filename="' . str_replace('+', '%20', urlencode($save_name)) . '"';
                } elseif (preg_match('/Firefox/', $user_agent)) {
                    return 'filename*="utf8\'\'' . str_replace('+', '%20', urlencode($save_name)) . '"';
                }
            }
            return 'filename="' . $save_name . '"';
        };
        $file_name = $process_file_name();

        if ($is_path) {
            if (is_file($data) && ($fp = fopen($data, 'rb')) !== false) {
                help::download_header($mime_type, filesize($data), $file_name);
                if (ob_get_level() !== 0 && ob_end_clean() === false) {
                    ob_clean();
                }
                while (!feof($fp) && ($source = fread($fp, 1048576)) !== false) {
                    echo $source;
                }
                fclose($fp);
                exit();
            }
            throw new Exception('read download file fail', 108);
        }
        help::download_header($mime_type, strlen($data), $file_name);
        exit($data);
    }

    //判断是否异步请求
    static function is_ajax(){
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $x_requested = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
            if ($x_requested === 'xmlhttprequest') {
                return true;
            }
        }
        return false;
    }

    //判断是否SSL协议
    static function is_ssl(){
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