<?php

class help{

    //加载分组模型、类库
    static function load($class, $path, $is_vendor = false){
        if ($is_vendor) {
            $path = trim($path, '/.... ') . '/';
        } else {
            $path = trim($path) ? 'module/' . $path . '/model/' : 'module/common/model/';
        }
        require_once PATH_ROOT . $path . $class . '.php';
        return class_exists($class) ? $class : null;
    }

    //构造URL
    static function url(){
        $amount = func_num_args();
        $param = func_get_args();
        if ($amount < 1) {
            return U_R_L;
        }

        $uri = trim(str_replace('.', '/', $param[0]), '/');
        if ($amount === 1) {
            return U_R_L . $uri;
        }

        if ($amount === 2 && is_array($param[1])) {
            $query = str_replace('&%23=', '#', http_build_query($param[1]));
            return U_R_L . $uri . '/?' . $query;
        }

        unset($param[0]);
        $query = str_replace('/#', '#', implode('/', $param));
        return U_R_L . $uri . '/' . $query;
    }

    //字典函数
    static function dict($word, $dict){
        $fp = fopen(DATA_DICT . $dict . '.txt', 'r');
        $buffer = array();
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            $data = explode('=', $line);
            $buffer[$data[0]] = $data[1];
        }
        fclose($fp);
        return $buffer[$word];
    }


    //获取IP
    static function ip(){
        if (defined('RUN_MODE') && RUN_MODE === 'cli') {
            return '127.0.0.1';
        }
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                return $_SERVER['HTTP_CLIENT_IP'];
            }
            return $_SERVER['REMOTE_ADDR'];
        }

        if (getenv('HTTP_X_FORWARDED_FOR')) {
            return getenv('HTTP_X_FORWARDED_FOR');
        }
        if (getenv('HTTP_CLIENT_IP')) {
            return getenv('HTTP_CLIENT_IP');
        }
        return getenv('REMOTE_ADDR');
    }

    //获取请求ip所在的省份和城市
    static function address($ip = '', $url = ''){
        $ip = $ip ? $ip : self::ip();
        //http://ipquery.sdo.com/getipinfo.php?ip=
        //http://ip.taobao.com/service/getIpInfo.php?ip=
        $url = $url ? $url : 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=';
        $data = iconv('gbk', 'utf-8//IGNORE', file_get_contents($url . $ip));
        preg_match_all('/[\x{4e00}-\x{9fa5}]+/u', $data, $address);
        return $address[0];
    }

    //获取移动设备的类型
    static function mobile(){
        if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']) {
            $mobile_list = array('iphone','ipad','ipod','android','windows phone','windows ce');
            $mobile_list = array_merge($mobile_list, array('opera mini','symbian','blackberry','kindle'));
            $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            foreach ($mobile_list as $equipment) {
                if (strpos($user_agent, $equipment) !== false) {
                    return $equipment;
                }
            }
        }
    }

    //检测银行卡
    static function is_bank_card($card_no, $bank){
        if (validate::check($card_no, 'length', '15,19')) {
            $prefix_number = system::config('bank.' . $bank);
            foreach ($prefix_number as $value) {
                if (strpos($card_no, $value) === 0) {
                    return true;
                }
            }
        }
        return false;
    }

    //发送下载文件头信息
    static function download_header($mime_type, $file_size, $file_name){
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; ' . $file_name);
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: private, no-transform, no-store, must-revalidate');
    }

    //数组多选处理
    static function array_sift($arrow, $target, $connector = ','){
        $string = '';
        $intersect = array_intersect($arrow, array_keys($target));
        foreach ($intersect as $key) {
            $string .= $target[$key] . $connector;
        }
        return trim($string, $connector);
    }

    //对象转成数组
    static function object_array($data){
        if (is_object($data)) {
            $data = get_object_vars($data);
            if (is_array($data)) {
                $data = array_map('self::object_array', $data);
            }
            return $data;
        }
    }

    //将数组或对象转换为xml（递归）
    static function data_xml($data, $tag = 'item', $with_id = false){
        $xml = '';
        foreach ($data as $key => $value) {
            $xml .= is_numeric($key) ? ($with_id ? "<$tag id=\"$key\">" : "<$tag>") : "<$key>";
            $xml .= (is_array($value) || is_object($value)) ? self::data_xml($value, $tag) : $value;
            $xml .= is_numeric($key) ? "</$tag>" : "</$key>";
        }
        return $xml;
    }

    //数组变ini配置数据
    static function array_ini($data){
        $string = '';
        if ($data && is_array($data)) {
            foreach ($data as $name => $option) {
                if ($option && is_array($option)) {
                    $string .= PHP_EOL . "[$name]" . PHP_EOL;
                    foreach ($option as $key => $value) {
                        $string .= $key . '=' . $value . PHP_EOL;
                    }
                }
            }
        }
        return trim($string, PHP_EOL);
    }

    //命名方式相互转换(snake-camel)
    static function snake_camel($data, $is_upper_case = false){
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $buffer[self::snake_camel($key, $is_upper_case)] = $value;
            }
            return isset($buffer) ? $buffer : array();
        }

        if (strpos($data, '_') === false) {
            $data = trim(strtolower(preg_replace('/([A-Z])/', '_$0', $data)), '_');
        } else {
            $data = str_replace(' ', '', ucwords(str_replace('_', ' ', $data)));
        }
        return $is_upper_case ? ucfirst($data) : lcfirst($data);
    }

    //接口数据类型转换
    static function data_format($data, $format, $dimension = 1){
        $result = null;
        if ($dimension < 1) {
            return settype($data, $format) ? $data : $result;
        }

        if ($dimension == 1) {
            //展平目标类型包(k-v)
            if (isset($format['int']) || isset($format['string'])) {
                $type_join = function () use ($format){
                    $buffer = array();
                    if (is_array($format) && $format) {
                        foreach ($format as $type => $field) {
                            $buffer += array_fill_keys(explode(',', $field), $type);
                        }
                    }
                    return $buffer;
                };
                $format = $type_join();
            }
            //一维(k-v)
            $option = array('int' => 0, 'string' => '', 'bool' => false,
                'float' => 0, 'array' => array(), 'object' => (object)array(), 'null' => null);
            foreach ($format as $field => $type) {
                $result[$field] = isset($data[$field]) ? self::data_format($data[$field], $type, 0) : $option[$type];
            }
        } else {
            //二维(record)
            foreach ($data as $key => $value) {
                $result[$key] = self::data_format($value, $format, 1);
            }
        }

        return $result;
    }


}