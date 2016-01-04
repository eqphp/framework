<?php

class s_identity{

    const PARSE_CARD_URL='http://baidu.uqude.com/baidu_mobile_war/idcard/dishi.action?';

    //验证华人姓名
    static function family_name($real_name){
        if (strlen($real_name) >= 6 && strlen($real_name) <= 15)
        if (regexp::match($real_name,'chinese')) {
            $first_name=substr($real_name,0,3);
            $odd_family_name=file::read('family_name',1);

            $odd_data=str_split($odd_family_name[0],3);
            if (in_array($first_name,$odd_data)) return true;

            $first_name=substr($real_name,0,6);
            $even_family_name=file::read('family_name',2);
            $even_data=explode(',',$even_family_name);
            if (in_array($first_name,$even_data)) return true;
        }
        return false;
    }

    //身份证号码检测
    static function is_cid($cid){
        //长度
        $reg_exp='/^(\d{18,18}|\d{15,15}|\d{17,17}x)$/';
        if (!preg_match($reg_exp,strtolower($cid))) return false;

        //城市
        $city_id=array(11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65,71,81,82,91);
        $cid_city=intval(substr($cid,0,2));
        if (!in_array($cid_city,$city_id)) return false;

        if (strlen($cid) === 18) {
            //ISO-加权因子
            if (strtolower(substr($cid,17,1)) !== self::get_right_no($cid)) return false;
            list($year,$lie)=array(substr($cid,6,4),10);
        } else {
            list($year,$lie)=array('19'.substr($cid,6,2),8);
        }

        //年(1912-2020)月(1-12)日(1-31)
        list($month,$day)=array(substr($cid,$lie,2),substr($cid,$lie+2,2));
        if (intval($year) < 1912 || intval($year) > 2020) return false;
        if (intval($month) < 1 || intval($month) > 12) return false;
        if (intval($day) < 1 || intval($day) > 31) return false;

        return true;
    }

    //cid 获取ISO-加权因子
    static function get_right_no($cid){
        $cid_base=substr($cid,0,17);
        $factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
        $verify=array('1','0','x','9','8','7','6','5','4','3','2');
        $number=0;
        for ($i=0; $i < 17; $i++) {
            $number+=intval(substr($cid_base,$i,1))*$factor[$i];
        }
        $num=$number%11;
        return $verify[$num];
    }

    //根据身份证号获取用户出生日期、性别、地址
    static function parse_card($card_no,$url=''){
        $birthday=$sex=$address='';
        empty($url) and $url=self::PARSE_CARD_URL;
        $param='cardNO='.$card_no.'&_='.time();
        $result=json_decode(file_get_contents($url.$param),true);
        if (isset($result['birthday']) && $result['birthday']) {
            $birthday=str_replace(array('年','月','日'),array('-','-',''),$result['birthday']);
        }
        if (isset($result['sex']) && $result['sex']) {
            $sex=$result['sex'] === '男' ? 'male' : 'female';
        }
        if (isset($result['address']) && $result['address']) {
            $address=$result['address'];
        }
        return compact('birthday','sex','address');
    }


}