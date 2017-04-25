<?php

class smarty3{

    protected static $object = null;

    static function get_instance(){
        if (!(isset(self::$object) && self::$object instanceof Smarty)) {
            include PATH_VENDOR . 'smarty/Smarty.class.php';
            self::$object = new Smarty;
        }
        return isset(self::$object) ? self::$object : null;
    }

    //赋值并显示模板
    static function show($tpl, $data = array(), $smarty = null){
        return $smarty->assign($data)->display($tpl);
    }

    //获取赋予模板的值
    static function get($tpl, $key = null){
        return $tpl->getTemplateVars($key);
    }

    //解析、注册分组组件
    static function parse_plugin($module, $tpl){
        foreach (array('function', 'modifier') as  $type) {
            $class_name = $module . '_' . $type;
            $file_name = PATH_MODULE . $module . '/plugin/'. $class_name .'.php';
            if (is_file($file_name)) {
                include $file_name;
            }
            $method_list = get_class_methods($class_name);
            if ($method_list && is_array($method_list)) {
                foreach ($method_list as $method) {
                    $tpl->registerPlugin($type, $method, array($class_name, $method));
                }
            }
        }
    }


    //配置视图环境
    static function tpl(){
        //基本配置
        $tpl = self::get_instance();
        $dir_url = self::process_view_config('common', $tpl);

        //设置静态资源、元信息
        $tpl->assign('url', U_R_L);
        $tpl->assign('dir', $dir_url);
        $tpl->assign(system::config('site'));

        //注册全局组件
        self::parse_plugin('common', $tpl);

        //处理分组业务
        if (defined('MODULE_NAME')) {
            self::process_module($tpl);
        }
        return $tpl;
    }


    static function process_view_config($module, $tpl){
        $smarty = system::config('smarty');
        //$tpl->debugging=$smarty[$module]['debug'];
        //$tpl->error_reporting=$smarty[$module]['error'];
        //$tpl->allow_php_templates=$smarty[$module]['allow_php'];

        $tpl->left_delimiter = $smarty[$module]['left'];
        $tpl->right_delimiter = $smarty[$module]['right'];

        $tpl->config_dir = $smarty[$module]['const'];
        $tpl->compile_dir = $smarty[$module]['compile'];
        $tpl->template_dir = $smarty[$module]['template'];

        $tpl->cache_dir = $smarty[$module]['path'];
        $tpl->caching = $smarty[$module]['caching'];
        $tpl->cache_lifetime = $smarty[$module]['expire'];

        return $smarty[$module]['dir_url'];
    }


    static function process_module($tpl){
        //赋值分组常量
        $module_path['url'] = help::url(MODULE_NAME) . '/';
        $module_path['script'] = URL_STATIC . MODULE_NAME . '/script/';
        $module_path['style'] = URL_STATIC . MODULE_NAME . '/style/';
        $module_path['image'] = URL_STATIC . MODULE_NAME . '/image/';
        $tpl->assign('module', $module_path);

        //注册分组私有模块组件
        self::parse_plugin(MODULE_NAME, $tpl);

        //处理分组私有事情
        $method = strtolower(MODULE_NAME) . '_init';
        if (method_exists(__CLASS__, $method)) {
            call_user_func(array(__CLASS__, $method), $tpl);
        }
    }

    static function admin_init($tpl){
        //$tpl->assign('manager', session::get('manager'));
    }


}