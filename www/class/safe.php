<?php
class safe{

    static $reg=array('tel'=>'/^((\(\d{3}\))|(\d{0}))?(13|14|15|18)\d{9}$/','email'=>'/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/','phone'=>'/^(0\d{2,3})?-?([2-9]\d{6,7})(-\d{1,5})?$/','phone_48'=>'/^(400|800)?-?\d{7}(-\d{1,5})?$/','qq'=>'/^[1-9]\d{4,9}$/','name'=>'/^[a-zA-Z][a-zA-Z0-9_]{4,17}$/','md5'=>'/^[a-z0-9]{32}$/','pwd'=>'/^(.){6,15}$/','money'=>'/^[0-9]+([.]{1}[0-9]{1,2})?$/','number'=>'/^[0-9]*[1-9][0-9]*$/','url'=>'/^http(s?):\/\/([\w-]+\.)+[\w-]+(\/[\\w\- \.\/?%&=]*)?/','cid'=>'/^\d{18}\d{15}/','zip'=>'/^\d{6}$/','address'=>'/^(.){0,50}$/','require'=>'/.+/','int'=>'/^[-\+]?\d+$/','float'=>'/^[-\+]?\d+(\.\d+)?$/','english'=>'/^[A-Za-z]+$/','name_cn'=>'/^[\u4E00-\u9FA5]{2,4}$/','account'=>'/^[\u4E00-\u9FA5\uf900-\ufa2d\w]{5,16}$/');

    //延伸的md5方法
    static function md5($str){
        return md5($str.config('safe|cc_key','safe'));
    }

    //返回正则匹配
    static function reg($str,$exp='email'){
        $reg=in_array($exp,array_keys(self::$reg)) ? self::$reg[$exp] : $exp;
        return preg_match($reg,$str);
    }

    //正则过滤
    static function filter(&$value,$exp='email'){
        if (!self::reg($value,$exp)) {
            $value=null;
        }
    }

    //特殊字符过滤
    static function str($str,$mode=true,$type=0){

        $risk=array('!@#$%&?~^<>`\'"\\','+-*/={}[]()|_,.:;');
        $risk=str_split($risk[$type],1);

        //彻底过滤
        if ($mode) {
            foreach ($risk as $key=>$value) {
                $str=str_replace($value,'',$str);
            }
            return $str;
        }

        //替换为指定字符
        $safe=array('zneqz,zatz,znumberz,zdollarz,zpercentz,zandz,zaskz,ztildez,zsquarez,zlessz,zgreaterz,zapostrophez,zsquotez,zdquotez,zbackslashz','zplusz,zminusz,zbyz,zmodz,zeqzzlcurlyz,zrcurlyz,zlbracketz,zrbracketz,zlparenz,zrparenz,zdividez,zunderlinez,zcommaz,zdotz,zcolonz,zsemicolonz');
        $safe=explode(',',$safe[$type]);
        foreach ($risk as $key=>$value) {
            $str=str_replace($value,$safe[$key],$str);
        }
        return $str;
    }

    //禁止字符过滤
    static function word($act_str){
        $word=config('word','safe');
        $risk_str=explode(',',$word['forbid']);
        $allow_str=str_replace($risk_str,$word['allow'],$act_str);
        return $allow_str;
    }

    //加密
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

    //解密
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

    //查询safe类方法
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、修正MD5：md5($str)<br>';
        $info.='2、正则验证：reg($str,$exp="email")<br>';
        $info.='3、字符过滤：str($str,$mode=true,$type=0)<br>';
        $info.='4、文明过滤：word($act_str)<br>';
        $info.='5、字符加密：encode($message,$password="eqphp")<br>';
        $info.='6、字符解密：decode($text,$password="eqphp")</font><br><br>';
        return $info;
    }


}