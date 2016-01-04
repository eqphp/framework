<?php
//rely on: session cookie
class secure{

    //延伸的md5方法
    static function md5($string){
        return md5($string.config('secure.key','secure'));
    }

    //返回令牌(user_agent,ip)
    static function token($string='',$ip=''){
        $secret_key=config('secure.key','secure');
        $agent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return md5($secret_key.$agent.$ip.$string);
    }

    //特殊字符过滤
    static function symbol($string,$is_strict=false){
        $risk='~^<>`\'"\\';
        $is_strict and $risk.='@!#$%&?+-*/={}[]()|,.:;';
        $risk=str_split($risk,1);
        return str_replace($risk,'',$string);
    }

    //获取6位密码保护串
    static function salt(){
        $symbol='!@#$%&?~^<>`+-*/={}[]()|_,.:;';
        $char=md5(time().rand(100,999));
        $salt=$char[rand(0,31)].$symbol[rand(0,28)].$char[rand(0,31)];
        return $salt.$char[rand(0,31)].$symbol[rand(0,28)].$char[rand(0,31)];
    }

    //加码
    static function encode($message,$password='eqphp'){
        $password=str_split(md5($password),16);
        $password=$password[rand(0,1)];
        if (strlen($message) == 2) {
            return strrev($password.base64_encode($message));
        }
        $message=base64_encode($message);
        $len=rand(1,strlen($message));
        $message=substr($message,0,$len).$password.substr($message,$len);
        $info=str_split($message,8);
        $lock=str_split(md5(time()),4);
        $buffer=null;
        foreach ($info as $key=>$value) {
            if ($key < 8) {
                $buffer[$key]=$lock[$key][3].$value.$lock[$key][0];
            } else {
                $buffer[$key]=$lock[$key%8][3].$value.$lock[$key%8][0];
            }
        }
        return strrev(implode($buffer));
    }

    //解码
    static function decode($text,$password='eqphp'){
        $text=strrev($text);
        $password=str_split(md5($password),16);
        if (strpos($text,$password[0]) !== false || strpos($text,$password[1]) !== false) {
            return base64_decode(str_replace($password,'',$text));
        }
        $info=str_split($text,10);
        $buffer=null;
        foreach ($info as $key=>$value) {
            $buffer[$key]=substr($value,1,8);
        }
        return trim(base64_decode(str_replace($password,'',implode($buffer))),"\x00..\x1F");
    }

    //加密
    static function encrypt($text,$key='eqphp'){
        $iv=mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB),MCRYPT_RAND);
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key,$text,MCRYPT_MODE_ECB,$iv));
    }

    //解密
    static function decrypt($text,$key='eqphp'){
        $iv=mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB),MCRYPT_RAND);
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$key,base64_decode($text),MCRYPT_MODE_ECB,$iv);
    }

    //csrf攻击
    static function csrf($mode,$csrf=''){
        $key=config('secure.csrf_name','secure');
        if ($mode === 'get') return session::get($key);
        if ($mode === 'check') {
            //Notice 是否只用一次并清掉cookie
            // input::cookie($key,$value,1);
            return ($csrf && $csrf === session::get($key));
        }
        if ($mode === 'set') {
            $value=substr(secure::token(time()),5,8);
            session::set($key,$value);
            input::cookie($key,$value,7200);
        }
        return true;
    }

    //xss检测（check）、过滤（filter）
    static function xss($string,$mode='check'){
        $regexp_list=config(null,'xss');
        if ($mode === 'check') {
            $risk=0;
            foreach ($regexp_list as $regexp) {
                if (preg_match($regexp,$string)) {
                    $risk++;
                }
            }
            return $risk;
        }
        return preg_replace($regexp_list,'',$string);
    }


}