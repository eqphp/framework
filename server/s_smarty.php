<?php

class s_smarty{

    protected static $object=null;

    static function get_instance(){
        if (!(isset(self::$object) && self::$object instanceof Smarty)) {
            self::$object=new Smarty;
        }
        return isset(self::$object) ? self::$object : null;
    }

    //赋值并显示模板
    static function show($tpl,$data=array(),$smarty=null){
        return $smarty->assign($data)->display($tpl);
    }

    //获取赋予模板的值
    static function get($tpl,$key=null){
        return $tpl->getTemplateVars($key);
    }

    //解析、注册分组组件
    static function parse_plugin($plugin,$tpl){
        foreach ($plugin as $type=>$data)
        foreach ($data as $class)
        if (class_exists($class)) {
            $class_method=get_class_methods($class);
            foreach ($class_method as $method)
            $tpl->registerPlugin($type,$method,array($class,$method));
        }
    }


    //配置视图环境
    static function tpl($group='home'){
        //基本配置
        $tpl=self::get_instance();
        self::process_view_config($group,$tpl);

        //网站元信息
        $tpl->assign(config(null,'site'));

        //设置目录常量
        $tpl->assign('url',U_R_L);
        $dir_data=system::set_url_dir(true);
        $tpl->assign('dir',$dir_data);


        //注册全局组件
        foreach (array('function','modifier') as $type) {
            $class='p_'.$type;
            include PATH_ROOT.'plugin/'.$class.'.php';
            if (class_exists($class)) {
                $method_data=get_class_methods($class);
                foreach ($method_data as $method) {
                    $tpl->registerPlugin($type,$method,array($class,$method));
                }
            }
        }

        //处理分组业务
        if (defined('GROUP_NAME')) {
            self::process_group($tpl);
        }
        return $tpl;
    }


    static function process_view_config($group,$tpl){
        $smarty=config(null,'smarty');
        // $tpl->error_reporting=$smarty[$group]['error'];
        // $tpl->debugging=$smarty[$group]['debug'];
        // $tpl->allow_php_templates=$smarty[$group]['php'];

        $tpl->template_dir=$smarty[$group]['template'];
        $tpl->compile_dir=$smarty[$group]['compile'];
        $tpl->config_dir=$smarty[$group]['config'];

        $tpl->caching=$smarty[$group]['caching'];
        $tpl->cache_dir=$smarty[$group]['root'];
        $tpl->cache_lifetime=$smarty[$group]['time'];

        $tpl->left_delimiter=$smarty[$group]['left'];
        $tpl->right_delimiter=$smarty[$group]['right'];
    }


    static function process_group($tpl){
        //赋值组常量
        $group_path['url']=route(GROUP_NAME).'/';
        $group_path['script']=URL_STATIC.GROUP_NAME.'/script/';
        $group_path['style']=URL_STATIC.GROUP_NAME.'/style/';
        $group_path['image']=URL_STATIC.GROUP_NAME.'/image/';
        $tpl->assign('group',$group_path);

        //注册分组私有模块组件
        if ($plugin=group_config(null,'plugin')) {
            self::parse_plugin($plugin,$tpl);
        }

        //处理分组私有事情
        $method=strtolower(GROUP_NAME).'_init';
        if (method_exists(__CLASS__,$method)) {
            call_user_func(array(__CLASS__,$method),$tpl);
        }
    }


}