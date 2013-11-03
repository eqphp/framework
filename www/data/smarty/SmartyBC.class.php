<?php
require(dirname(__FILE__) . '/Smarty.class.php');

class SmartyBC extends Smarty {

    public $_version = self::SMARTY_VERSION;

    public function __construct(array $options=array()) {
        parent::__construct($options);

        $this->registerPlugin('block', 'php', 'smarty_php_tag');
    }

    public function assign_by_ref($tpl_var, &$value) {
        $this->assignByRef($tpl_var, $value);
    }

    public function append_by_ref($tpl_var, &$value, $merge = false) {
        $this->appendByRef($tpl_var, $value, $merge);
    }

    public function clear_assign($tpl_var) {
        $this->clearAssign($tpl_var);
    }


    public function register_function($function, $function_impl, $cacheable=true, $cache_attrs=null) {
        $this->registerPlugin('function', $function, $function_impl, $cacheable, $cache_attrs);
    }


    public function unregister_function($function) {
        $this->unregisterPlugin('function', $function);
    }


    public function register_object($object, $object_impl, $allowed = array(), $smarty_args = true, $block_methods = array()) {
        settype($allowed, 'array');
        settype($smarty_args, 'boolean');
        $this->registerObject($object, $object_impl, $allowed, $smarty_args, $block_methods);
    }


    public function unregister_object($object) {
        $this->unregisterObject($object);
    }


    public function register_block($block, $block_impl, $cacheable=true, $cache_attrs=null) {
        $this->registerPlugin('block', $block, $block_impl, $cacheable, $cache_attrs);
    }


    public function unregister_block($block) {
        $this->unregisterPlugin('block', $block);
    }


    public function register_compiler_function($function, $function_impl, $cacheable=true) {
        $this->registerPlugin('compiler', $function, $function_impl, $cacheable);
    }


    public function unregister_compiler_function($function) {
        $this->unregisterPlugin('compiler', $function);
    }


    public function register_modifier($modifier, $modifier_impl) {
        $this->registerPlugin('modifier', $modifier, $modifier_impl);
    }


    public function unregister_modifier($modifier) {
        $this->unregisterPlugin('modifier', $modifier);
    }


    public function register_resource($type, $functions) {
        $this->registerResource($type, $functions);
    }


    public function unregister_resource($type) {
        $this->unregisterResource($type);
    }


    public function register_prefilter($function) {
        $this->registerFilter('pre', $function);
    }


    public function unregister_prefilter($function) {
        $this->unregisterFilter('pre', $function);
    }


    public function register_postfilter($function) {
        $this->registerFilter('post', $function);
    }


    public function unregister_postfilter($function) {
        $this->unregisterFilter('post', $function);
    }


    public function register_outputfilter($function) {
        $this->registerFilter('output', $function);
    }


    public function unregister_outputfilter($function) {
        $this->unregisterFilter('output', $function);
    }


    public function load_filter($type, $name) {
        $this->loadFilter($type, $name);
    }


    public function clear_cache($tpl_file = null, $cache_id = null, $compile_id = null, $exp_time = null) {
        return $this->clearCache($tpl_file, $cache_id, $compile_id, $exp_time);
    }


    public function clear_all_cache($exp_time = null) {
        return $this->clearCache(null, null, null, $exp_time);
    }


    public function is_cached($tpl_file, $cache_id = null, $compile_id = null) {
        return $this->isCached($tpl_file, $cache_id, $compile_id);
    }


    public function clear_all_assign() {
        $this->clearAllAssign();
    }


    public function clear_compiled_tpl($tpl_file = null, $compile_id = null, $exp_time = null) {
        return $this->clearCompiledTemplate($tpl_file, $compile_id, $exp_time);
    }


    public function template_exists($tpl_file) {
        return $this->templateExists($tpl_file);
    }


    public function get_template_vars($name=null) {
        return $this->getTemplateVars($name);
    }


    public function get_config_vars($name=null) {
        return $this->getConfigVars($name);
    }


    public function config_load($file, $section = null, $scope = 'global') {
        $this->ConfigLoad($file, $section, $scope);
    }


    public function get_registered_object($name) {
        return $this->getRegisteredObject($name);
    }


    public function clear_config($var = null) {
        $this->clearConfig($var);
    }


    public function trigger_error($error_msg, $error_type = E_USER_WARNING) {
        trigger_error("Smarty error: $error_msg", $error_type);
    }

}


function smarty_php_tag($params, $content, $template, &$repeat) {
    eval($content);
    return '';
}
?>