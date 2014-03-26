<?php
class tpl{

    protected static $object=null;

    static function get_instance(){
        if (!(isset(self::$object) && self::$object instanceof Smarty)) {
            self::$object=new Smarty;
        }
        return isset(self::$object) ? self::$object : null;
    }

    //赋值并显示模板
    static function show($tpl,$data=array(),$smarty=null){
        foreach ($data as $key=>$value) {
            $smarty->assign($key,$value);
        }
        return $smarty->display($tpl);
    }

    //获取赋予模板的值
    static function get($tpl,$key=null){
        return $tpl->getTemplateVars($key);
    }

    //注册组建
    protected static function reg($class,$function,$smarty,$type='function'){
        return $smarty->registerPlugin($type,$function,array($class,$function));
    }

    //批量注册组件
    protected static function register($plugins=array(),$smarty=null){
        $type=null;
        foreach ($plugins as $plug) {
            $type=(count($plug) > 2) ? $plug[2] : 'function';
            if (count($plug) < 2 || !in_array($type,array('function','block','modifier'))) {
                http::out($plug);
            }
            self::reg($plug[0],$plug[1],$smarty,$type);
        }
        return true;
    }

    //组件解析、注册
    static function parse_plugins($plugins,$tpl){
        foreach ($plugins as $p_type=>$p_class_str) {
            $p_class_arr=explode(',',$p_class_str);

            foreach ($p_class_arr as $p_class) {
                if (class_exists($p_class)) {
                    $class_method=get_class_methods($p_class);

                    foreach ($class_method as $p_way) {
                        self::reg($p_class,$p_way,$tpl,$p_type);
                    }
                }
            }
        }

    }

    //查询tpl类方法
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、模版对象：get_instance()<br>';
        $info.='2、赋值显示：show($tpl,$data=array(),$smarty=null)<br>';
        $info.='3、获取变量：get($tpl,$key=null)</font><br><br>';
        return $info;
    }


}