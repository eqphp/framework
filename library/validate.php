<?php

//rely on: util http
class validate{

    //单项数据验证
    static function check($value, $type, $rule){
        $type = trim(strtolower($type));
        switch ($type) {
            //是否在指定范围值之内，逗号分隔字符串或者数组
            case 'in':
            case 'not_in':
                $range = is_array($rule) ? $rule : explode(',', $rule);
                return ($type == 'in') ? in_array($value, $range) : !in_array($value, $range);

            //在/不在某个区间内
            case 'between':
            case 'not_between':
                list($min, $max) = is_array($rule) ? $rule : explode(',', $rule);
                return ($type == 'between') ? ($value >= $min && $value <= $max) : ($value < $min || $value > $max);

            //是否相等
            case 'equal':
            case 'not_equal':
                return ($type === 'equal') ? ($value === $rule) : ($value !== $rule);

            //长度
            case 'length':
                $length = mb_strlen($value, 'utf-8');
                if (strpos($rule, ',')) {
                    //指定长度区间内
                    list($min, $max) = explode(',', $rule);
                    return $length >= $min && $length <= $max;
                } else {
                    //长度相等
                    return $length == $rule;
                }

            //有效期
            case 'expire':
                $now_time = time();
                list($start, $end) = explode(',', $rule);
                $start = is_numeric($start) ? $start : strtotime($start);
                $end = is_numeric($end) ? $end : strtotime($end);
                return $now_time >= $start && $now_time <= $end;

            //array('function','class::method',array $param)
            case 'function':
                array_unshift($rule[1], $value);
                return call_user_func_array($rule[0], $rule[1]);

            //array('callback','object','method',array $param)
            case 'callback':
                array_unshift($rule[2], $value);
                return call_user_func_array(array($rule[0], $rule[1]), $rule[2]);

            //使用正则验证
            case 'regexp':
            default:
                return util::match($value, $rule);
        }
    }

    //单项验证、提示错误信息check_tip($age,['between',[25,45]],[2,'age error'])
    static function check_tip($value, $option, $tip){
        if (self::check($value, $option[0], $option[1])) {
            return true;
        }
        if (is_array($tip)) {
            $data['error'] = $tip[0];
            $data['message'] = $tip[1];
            if (isset($tip[2])) {
                $data['data'] = $tip[2];
            }
            http::json($data);
        }
        http::script($tip, 'alert');
    }

    //批量验证、提示错误
    static function verify(array $data, $option){
        foreach ($data as $key => $value) {
            self::check_tip($value, $option[$key][0], $option[$key][1]);
        }
    }


}