<?php

class regexp{

    const PHONE='/^(1(([3,8][0-9])|(4[5,7])|(5[^4])|(7[0,6,7,8])))\d{8}$/';
    const EMAIL='/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/';
    const TELEPHONE='/^(0\d{2,3})?-?([2-9]\d{6,7})(-\d{1,5})?$/';
    const HOT_LINE='/^(400|800)-(\d{3})-(\d{4})?$/';
    const QQ='/^[1-9]\d{4,9}$/';
    const ACCOUNT='/^[a-zA-Z][a-zA-Z0-9_]{4,17}$/';
    const MD5='/^[a-f0-9]{32}$/';
    const PASSWORD='/^(.){6,18}$/';
    const MONEY='/^[0-9]+([.][0-9]{1,2})?$/';
    const NUMBER='/^\-?[0-9]*\.?[0-9]*$/';
    const NUMERIC='/^\d+$/';
    const URL='/^http(s?):\/\/([\w-]+\.)+[\w-]+(\/[\\w\- \.\/?%&=]*)?/';
    const CID='/^\d{15}$|^\d{17}(\d|X|x)$/';
    const ZIP='/^\d{6}$/';
    const ADDRESS='/^(.){0,64}$/';
    const INT='/^[-\+]?\d+$/';
    const FLOAT='/^[-\+]?\d+(\.\d+)?$/';
    const ENGLISH='/^[A-Za-z]+$/';
    const CHINESE='/^[\x{4e00}-\x{9fa5}]+$/u';
    const CHINESE_NAME='/^[\x{4e00}-\x{9fa5}]{2,5}$/u';
    const NAME='/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u';
    const FILE_NAME='/^[^\/:*?"<>|,\\\]+$/';
    const ID='/^[1-9]{1}[0-9]{0,9}$/';
    const UUID='/^[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$/';
    const IMAGE='/<img[^\/>src]+src="([^"]+)"[^\/>]*\/?/';
    const BUSINESS_LICENSE='/^\d{13}$|^\d{14}([0-9]|X|x)$|^\d{6}(N|n)(A|a|B|b)\d{6}(X|x)$/';


    //返回当前类中定义的所有正则表达式
    static function get($is_lower_key=true){
        $self = new ReflectionClass(__CLASS__);
        $mode=$is_lower_key ? CASE_LOWER : CASE_UPPER;
        return array_change_key_case($self->getConstants(),$mode);
    }

    //正则匹配
    static function match($string,$regexp='email'){
        $constant=__CLASS__.'::'.strtoupper($regexp);
        if (defined($constant)) {
            $regexp=constant($constant);
        }
        return preg_match($regexp,$string);
    }

    //正则过滤
    static function filter($value,$regexp='email',$default=''){
        return self::match($value,$regexp) ? $value : $default;
    }

}