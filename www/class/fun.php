<?php
class fun{

    //字符截取
    static function str_cut($str,$length,$from=0){
        $str_len=mb_strlen($str,'gb2312');
        $add_str=($str_len > $length) ? '…' : '';
        $exp='#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}';
        $exp.='((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$length.'}).*#s';
        return preg_replace($exp,'$1',$str).$add_str;
    }

    //将数组连按照key=value成字符串
    static function arr_str($info_arr,$connector='&',$is_urlencode=false){
        $feild_info=null;
        if ($info_arr) {
            foreach ($info_arr as $key=>$value) {
                $is_urlencode && $value=urlencode($value);
                $feild_info.=$key.'='.$value.$connector;
            }
        }
        return trim($feild_info,$connector);
    }

    //处理记录集(php5.5内置)
    static function array_column($data,$key='id',$column=null){

        $buffer=null;

        if ($column) {
            //k=>v
            if (strpos($column,',') === false) {
                foreach ($data as $value) {
                    $buffer[$value[$key]]=$value[$column];
                }
                return $buffer;
            }

            //k=arr
            $field=explode(',',$column);
            foreach ($data as $value) {
                $id=$value[$key];
                array_walk($value,function ($v,$k) use (&$value,$field){
                    if (!in_array($k,$field)) unset($value[$k]);
                });
                $buffer[$id]=$value;
            }
            return $buffer;
        }

        //id_arr
        foreach ($data as $value) {
            $buffer[]=$value[$key];
        }
        return $buffer;
    }


    //输出xml字符文档
    static function xml($mix_data,$root='rss',$code='utf-8'){
        $xml='<?xml version="1.0" encoding="'.$code.'"?>';
        $xml.='<'.$root.'>'.self::data_xml($mix_data).'</'.$root.'>';
        return $xml;
    }

    //将数组或对象转换为xml（递归）
    static function data_xml($mix_data,$num_tag='item',$mode=false){
        $xml=null;
        foreach ($mix_data as $key=>$val) {

            if ($mode) {
                $xml.=is_numeric($key) ? '<'.$num_tag.' id="'.$key.'">' : '<'.$key.'>';
            } else {
                $xml.=is_numeric($key) ? '<'.$num_tag.'>' : '<'.$key.'>';
            }

            $xml.=(is_array($val) || is_object($val)) ? self::data_xml($val) : $val;
            $xml.=is_numeric($key) ? '</'.$num_tag.'>' : '</'.$key.'>';
        }
        return $xml;
    }

    //数组变ini配置数据
    static function arr_ini($data){
        $str='';
        if ($data && is_array($data)) {
            foreach ($data as $name=>$info) {

                if ($info && is_array($info)) {
                    $str.="\r\n[$name]\r\n";
                    foreach ($info as $key=>$value) {
                        $str.=$key.'='.$value."\r\n";
                    }

                }

            }
        }
        return trim($str,"\r\n");
    }

    //查询fun类方法
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、字符截取：str_cut($str,$length,$from=0)<br>';
        $info.='2、键值组合：arr_str($info_arr,$connector="&")<br>';
        $info.='3、二维取列：array_column($data,$key="id",$column=null)<br>';
        $info.='4、输出XML：xml($mix_data,$root="rss",$code="utf-8")<br>';
        $info.='5、转为XML：data_xml($mix_data)<br>';
        $info.='6、转为INI：arr_ini($data)</font><br><br>';
        return $info;
    }


}