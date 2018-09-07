<?php

class help{

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

    //国际化语言包解析
    static function lang($name, $i18n = ''){
        if (empty($i18n)) {
            $i18n = session::get('i18n');
            $i18n = empty($i18n) ? 'cn' : $i18n;
        }
        $module = '';
        if (strpos($name, '::')) {
            list($module, $name) = explode('::', $name);
            $module = $module . '/';
        }
        list($file_name, $map) = explode(':', $name);
        $file = DATA_LANG . $i18n . '/' . $module . $file_name . '.php';
        $key = md5($file);
        if (empty($GLOBALS['_I18N'][$key])) {
            $GLOBALS['_I18N'][$key] = include($file);
        }
        return util::array_get($GLOBALS['_I18N'][$key], $map);
    }

    //输出时过滤html标签、反转义字符
    static function put($string, $allow_tags = ''){
        if (!get_magic_quotes_gpc()) {
            $string = stripslashes($string);
        }
        if (empty($allow_tags)) {
            $allow_tags = '<a><img><span><b><i><em><cite><strong><br><hr>';
            $allow_tags .= '<ul><ol><li><p><h1><h2><h3><h4><h5><h6><table><tr><th><td>';
        }
        return strip_tags($string, $allow_tags);
    }

    //字符截取
    static function cut($string, $length = 15, $tail = '...'){
        if (mb_strlen($string) <= $length) {
            return $string;
        }
        return mb_substr($string, 0, $length, 'UTF-8') . $tail;
    }

    //转换为指定精度倍数的整数
    static function int($number, $precision = 0, $is_round = false){
        $precision = (int)$precision;
        $is_round and $number = round($number, $precision);
        $number *= pow(10, $precision);
        return (int)$number;
    }

    //转换为指定精度位数的浮点数
    static function float($number, $precision = 2, $is_round = false){
        if ($is_round) {
            return round($number, $precision);
        }
        $by = pow(10, (int)$precision);
        $number = (int)($number * $by);
        return $number / $by;
    }

    //生成限定数量的定长编号
    static function number($prefix, $length, $amount = 100){
        if ($amount < 100) {
            $amount += 100;
        } else {
            $amount *= 10;
        }
        $buffer = array();
        $max = pow(10, $length) - 1;
        for ($i = $amount; $i > 0; $i--) {
            $buffer[] = $prefix . str_pad(mt_rand(1, $max), $length, 0, STR_PAD_LEFT);
        }
        return array_unique($buffer);
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

    //处理记录集(php5.5内置)
    static function array_column($data, $column, $key = 'id'){
        $buffer = array();
        if ($data && is_array($data)) {
            //[key:value, ... N]
            if (strpos($column, ',') === false) {
                foreach ($data as $value) {
                    $buffer[$value[$key]] = $value[$column];
                }
                return $buffer;
            }
            //[key:[k:v, ... n], ... N]
            $field_list = explode(',', $column);
            foreach ($data as $value) {
                $id = $value[$key];
                foreach ($field_list as $field) {
                    $buffer[$id][$field] = $value[$field];
                }
            }
        }
        return $buffer;
    }

    //数组添加指定map的键值
    static function array_set(&$data, $map, $value){
        if (is_null($map)) {
            return $data = $value;
        }
        $maps = explode('.', $map);
        while (count($maps) > 1) {
            $map = array_shift($maps);
            if (!isset($data[$map]) or !is_array($data[$map])) {
                $data[$map] = array();
            }
            $data =& $data[$map];
        }
        $data[array_shift($maps)] = $value;
        return $data;
    }

    //数组卸载指定的map
    static function array_unset(&$data, $map){
        $maps = explode('.', $map);
        while (count($maps) > 1) {
            $map = array_shift($maps);
            if (!isset($data[$map]) or !is_array($data[$map])) {
                return;
            }
            $data =& $data[$map];
        }
        unset($data[array_shift($maps)]);
    }

    //数组变ini配置数据
    static function array_ini($data){
        $string = '';
        if ($data && is_array($data)) {
            foreach ($data as $name => $option) {
                if (is_array($option)) {
                    $string .= PHP_EOL . "[$name]" . PHP_EOL;
                    foreach ($option as $key => $value) {
                        $string .= $key . '=' . $value . PHP_EOL;
                    }
                } else {
                    $string .= $name . '=' . (string)$option . PHP_EOL;
                }
            }
        }
        return trim($string, PHP_EOL);
    }

    //记录集指定列集合
    static function array_field($data, $column = 'id', $is_string = false){
        $buffer = array();
        if ($data && is_array($data)) {
            //[field:[k1,k2, ... kn], ... KN]
            if (strpos($column, ',')) {
                $field_list = explode(',', $column);
                foreach ($data as $value) {
                    foreach ($field_list as $field) {
                        $buffer[$field][] = $value[$field];
                    }
                }
                return $buffer;
            }
            //[k1,k2, ... kn] || k1,k2, ... kn
            foreach ($data as $value) {
                array_push($buffer, $value[$column]);
            }
            $buffer = array_unique($buffer);
            $is_string and $buffer = implode(',', $buffer);
        }
        return $buffer;
    }

    //记录集排序取列
    static function array_field_sort($data, $column = 'id'){
        $buffer = array();
        if ($data && is_array($data)) {
            //[field:[k1:v1,k2:v2, ... kn:vn], ... KN]
            if (strpos($column, ',')) {
                $field_list = explode(',', $column);
                foreach ($data as $key => $value) {
                    foreach ($field_list as $field) {
                        $buffer[$field][$key] = $value[$field];
                    }
                }
                return $buffer;
            }
            //[k1:v1,k2:v2, ... kn:vn]
            foreach ($data as $key => $value) {
                $buffer[$key] = $value[$column];
            }
        }
        return $buffer;
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

    //无限分类递归
    static function tree($data, $parent_id = 0, $level = 0){
        static $tree = array();
        foreach ($data as $key => $value) {
            if ($value['parent_id'] == $parent_id) {
                $value['level'] = $level;
                $tree[] = $value;
                unset($data[$key]);
                self::tree($data, $value['id'], $level + 1);
            }
        }
        return $tree;
    }

    //生成树结构
    static function build_tree($data, $parent_id = 0, $level = 1, $field = array('id' => 'id', 'parent_id' => 'parent_id', 'child' => 'child')){
        $buffer = array();
        foreach ($data as &$value) {
            if ($value[$field['parent_id']] == $parent_id) {
                $value['level'] = $level;
                $temp = self::build_tree($data, $value[$field['id']], $value['level'] + 1, $field);
                $temp and $value[$field['child']] = $temp;
                unset($value['level']);
                $buffer[] = $value;
            }
        }
        return $buffer;
    }

    //csrf攻击
    static function csrf($mode, $csrf = ''){
        $key = system::config('system.secure.csrf_name');
        if ($mode === 'get') {
            return session::get($key);
        }
        if ($mode === 'check') {
            //Notice 是否只用一次并清掉cookie
            //http::cookie($key,$value,true);
            return ($csrf && $csrf === session::get($key));
        }
        if ($mode === 'set') {
            $value = substr(util::token(time()), 5, 8);
            session::set($key, $value);
            http::cookie($key, $value, 7200);
            return $value;
        }
    }

    //xss检测（check）、过滤（filter）
    static function xss($string, $mode = 'check'){
        $regexp_list = util::meta('xss');
        if ($mode === 'check') {
            $risk = 0;
            foreach ($regexp_list as $regexp) {
                if (preg_match($regexp, $string)) {
                    $risk++;
                }
            }
            return $risk;
        }
        return preg_replace($regexp_list, '', $string);
    }

    //获取IP
    static function ip(){
        $ip = '';
        if (defined('RUN_MODE') && RUN_MODE === 'cli') {
            $ip = '127.0.0.1';
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        }
        return $ip;
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

    //检测银行卡
    static function is_bank_card($card_no, $prefix_list){
        $card_no_length = strlen($card_no);
        if ($card_no_length >= 12 && $card_no_length <= 19) {
            foreach ($prefix_list as $value) {
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

}