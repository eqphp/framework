<?php
//rely on: http regexp
class validate{

    //单项数据验证
    static function check($value,$rule,$type='regexp'){
        $type=strtolower(trim($type));
        switch($type){

        case 'in': //是否在指定范围值之内，逗号分隔字符串或者数组
        case 'not_in':
            $range=is_array($rule) ? $rule : explode(',',$rule);
            return ($type == 'in') ? in_array($value,$range) : !in_array($value,$range);

        case 'between': //在某个区间内
        case 'not_between': //在某个区间外
            list($min,$max)=is_array($rule) ? $rule : explode(',',$rule);
            return ($type == 'between') ? ($value >= $min && $value <= $max) : ($value < $min || $value > $max);

        case 'equal': //是否相等
        case 'not_equal': //是否不等
            return ($type === 'equal') ? ($value === $rule) : ($value !== $rule);

        case 'length': //长度
            $length=mb_strlen($value,'utf-8');
            if (strpos($rule,',')) { //指定长度区间内
                list($min,$max)=explode(',',$rule);
                return $length >= $min && $length <= $max;
            } else { //长度相等
                return $length == $rule;
            }

        case 'expire': //有效期
            $now_time=time();
            list($start,$end)=explode(',',$rule);
            $start=is_numeric($start) ? $start : strtotime($start);
            $end=is_numeric($end) ? $end : strtotime($end);
            return $now_time >= $start && $now_time <= $end;

        case 'regexp':
        default: //默认使用正则验证
            return regexp::match($value,$rule);
        }
    }

    //单项数据验证并提示错误信息(check_tip($age,[[25,45],'in',[2,'age error']]))
    static function check_tip($value,$option){
        if (self::check($value,$option[0],$option[1])) return true;
        if (is_array($option[2])){
            $tip['error']=$option[2][0];
            $tip['message']=$option[2][1];
            isset($option[2][2]) and $tip['data']=$option[2][2];
            http::json($tip);
        }
        http::script($option[2],'alert');
    }

    //扩展级验证
    //函数验证,array('function','class_name::fun_name',$param)
    //[对象]方法验证,array('callback','obj_name','fun_name',$param)
    //是否相等array('confirm','form_option_name')
    //default:self::check() array('regex',$param)
    static function verify($value,$option){
        switch (strtolower(trim($option[0]))) {
        case 'function':
            $param=array_unshift($option[2],$value);
            return call_user_func_array($option[1],$param);

        case 'callback':
            $param=array_unshift($option[3],$value);
            return call_user_func_array(array($option[1],$option[2]),$param);

        case 'confirm': return $value===$option[1];
        default:
            return self::check($value,$option[0],$option[1]);
        }

    }

    //扩展级验证并提示错误信息
    static function verify_tip($value,$option,$tip){
        if (self::verify($value,$option)) return true;
        if (is_array($tip)) http::json($tip);
        http::script($tip,'alert');
    }

    //数据集验证(批量验证)
    static function valid($data,$option,$tip){
        if (!is_array($data)) return false;
        $index=0;
        foreach ($data as $key=>$value) {
            self::verify_tip($value,$option[$index],$tip[$index]);
            $index++;
        }
        return true;
    }


}