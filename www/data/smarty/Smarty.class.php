<?php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', dirname(__FILE__) . DS);
}
if (!defined('SMARTY_SYSPLUGINS_DIR')) {
    define('SMARTY_SYSPLUGINS_DIR', SMARTY_DIR . 'sysplugins' . DS);
}
if (!defined('SMARTY_PLUGINS_DIR')) {
    define('SMARTY_PLUGINS_DIR', SMARTY_DIR . 'plugins' . DS);
}
if (!defined('SMARTY_MBSTRING')) {
    define('SMARTY_MBSTRING', function_exists('mb_strlen'));
}
if (!defined('SMARTY_RESOURCE_CHAR_SET')) {
    define('SMARTY_RESOURCE_CHAR_SET', SMARTY_MBSTRING ? 'UTF-8' : 'ISO-8859-1');
}
if (!defined('SMARTY_RESOURCE_DATE_FORMAT')) {
    define('SMARTY_RESOURCE_DATE_FORMAT', '%b %e, %Y');
}
if (!defined('SMARTY_SPL_AUTOLOAD')) {
    define('SMARTY_SPL_AUTOLOAD', 0);
}
if (SMARTY_SPL_AUTOLOAD && set_include_path(get_include_path() . PATH_SEPARATOR . SMARTY_SYSPLUGINS_DIR) !== false) {
    $registeredAutoLoadFunctions = spl_autoload_functions();
    if (!isset($registeredAutoLoadFunctions['spl_autoload'])) {
        spl_autoload_register();
    }
} else {
    spl_autoload_register('smartyAutoload');
}

include_once SMARTY_SYSPLUGINS_DIR.'smarty_internal_data.php';
include_once SMARTY_SYSPLUGINS_DIR.'smarty_internal_templatebase.php';
include_once SMARTY_SYSPLUGINS_DIR.'smarty_internal_template.php';
include_once SMARTY_SYSPLUGINS_DIR.'smarty_resource.php';
include_once SMARTY_SYSPLUGINS_DIR.'smarty_internal_resource_file.php';
include_once SMARTY_SYSPLUGINS_DIR.'smarty_cacheresource.php';
include_once SMARTY_SYSPLUGINS_DIR.'smarty_internal_cacheresource_file.php';



class Smarty extends Smarty_Internal_TemplateBase {

    const SMARTY_VERSION = 'Smarty-3.1.11';


    const SCOPE_LOCAL = 0;
    const SCOPE_PARENT = 1;
    const SCOPE_ROOT = 2;
    const SCOPE_GLOBAL = 3;

    const CACHING_OFF = 0;
    const CACHING_LIFETIME_CURRENT = 1;
    const CACHING_LIFETIME_SAVED = 2;

    const COMPILECHECK_OFF = 0;
    const COMPILECHECK_ON = 1;
    const COMPILECHECK_CACHEMISS = 2;

    const PHP_PASSTHRU = 0;
    const PHP_QUOTE = 1;
    const PHP_REMOVE = 2;
    const PHP_ALLOW = 3;

    const FILTER_POST = 'post';
    const FILTER_PRE = 'pre';
    const FILTER_OUTPUT = 'output';
    const FILTER_VARIABLE = 'variable';

    const PLUGIN_FUNCTION = 'function';
    const PLUGIN_BLOCK = 'block';
    const PLUGIN_COMPILER = 'compiler';
    const PLUGIN_MODIFIER = 'modifier';
    const PLUGIN_MODIFIERCOMPILER = 'modifiercompiler';


    public static $global_tpl_vars = array();


    public static $_previous_error_handler = null;

    public static $_muted_directories = array();

    public static $_MBSTRING = SMARTY_MBSTRING;

    public static $_CHARSET = SMARTY_RESOURCE_CHAR_SET;

    public static $_DATE_FORMAT = SMARTY_RESOURCE_DATE_FORMAT;

    public static $_UTF8_MODIFIER = 'u';
    
    public static $_IS_WINDOWS = false;
    
    public $auto_literal = true;

    public $error_unassigned = false;

    public $use_include_path = false;

    private $template_dir = array();

    public $joined_template_dir = null;

    public $joined_config_dir = null;

    public $default_template_handler_func = null;

    public $default_config_handler_func = null;

    public $default_plugin_handler_func = null;

    private $compile_dir = null;

    private $plugins_dir = array();

    private $cache_dir = null;

    private $config_dir = array();

    public $force_compile = false;

    public $compile_check = true;

    public $use_sub_dirs = false;

    public $allow_ambiguous_resources = false;

    public $caching = false;

    public $merge_compiled_includes = false;

    public $cache_lifetime = 3600;

    public $force_cache = false;

    public $cache_id = null;

    public $compile_id = null;

    public $left_delimiter = "{";

    public $right_delimiter = "}";

    public $security_class = 'Smarty_Security';

    public $security_policy = null;

    public $php_handling = self::PHP_PASSTHRU;

    public $allow_php_templates = false;

    public $direct_access_security = true;

    public $debugging = false;

    public $debugging_ctrl = 'NONE';

    public $smarty_debug_id = 'SMARTY_DEBUG';

    public $debug_tpl = null;

    public $error_reporting = null;

    public $get_used_tags = false;

    public $config_overwrite = true;

    public $config_booleanize = true;

    public $config_read_hidden = false;

    public $compile_locking = true;

    public $cache_locking = false;

    public $locking_timeout = 10;

    public $template_functions = array();

    public $default_resource_type = 'file';

    public $caching_type = 'file';

    public $properties = array();

    public $default_config_type = 'file';

    public $template_objects = array();

    public $cache_modified_check = false;

    public $registered_plugins = array();

    public $plugin_search_order = array('function', 'block', 'compiler', 'class');

    public $registered_objects = array();

    public $registered_classes = array();

    public $registered_filters = array();

    public $registered_resources = array();

    public $_resource_handlers = array();

    public $registered_cache_resources = array();

    public $_cacheresource_handlers = array();

    public $autoload_filters = array();

    public $default_modifiers = array();

    public $escape_html = false;

    public static $_smarty_vars = array();

    public $start_time = 0;

    public $_file_perms = 0644;

    public $_dir_perms = 0771;

    public $_tag_stack = array();

    public $smarty;

    public $_current_file = null;

    public $_parserdebug = false;

    public $merged_templates_func = array();

    public function __construct() {

        $this->smarty = $this;
        if (is_callable('mb_internal_encoding')) {
            mb_internal_encoding(Smarty::$_CHARSET);
        }
        $this->start_time = microtime(true);

        $this->setTemplateDir('.' . DS . 'templates' . DS)
            ->setCompileDir('.' . DS . 'templates_c' . DS)
            ->setPluginsDir(SMARTY_PLUGINS_DIR)
            ->setCacheDir('.' . DS . 'cache' . DS)
            ->setConfigDir('.' . DS . 'configs' . DS);

        $this->debug_tpl = 'file:' . dirname(__FILE__) . '/debug.tpl';
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $this->assignGlobal('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
        }
    }

    public function __destruct() {

    }

    public function __clone() {
        $this->smarty = $this;
    }


    public function __get($name) {
        $allowed = array(
        'template_dir' => 'getTemplateDir',
        'config_dir' => 'getConfigDir',
        'plugins_dir' => 'getPluginsDir',
        'compile_dir' => 'getCompileDir',
        'cache_dir' => 'getCacheDir',
        );

        if (isset($allowed[$name])) {
            return $this->{$allowed[$name]}();
        } else {
            trigger_error('Undefined property: '. get_class($this) .'::$'. $name, E_USER_NOTICE);
        }
    }


    public function __set($name, $value) {
        $allowed = array(
        'template_dir' => 'setTemplateDir',
        'config_dir' => 'setConfigDir',
        'plugins_dir' => 'setPluginsDir',
        'compile_dir' => 'setCompileDir',
        'cache_dir' => 'setCacheDir',
        );

        if (isset($allowed[$name])) {
            $this->{$allowed[$name]}($value);
        } else {
            trigger_error('Undefined property: ' . get_class($this) . '::$' . $name, E_USER_NOTICE);
        }
    }


    public function templateExists($resource_name) {

        $save = $this->template_objects;
        $tpl = new $this->template_class($resource_name, $this);

        $result = $tpl->source->exists;
        $this->template_objects = $save;
        return $result;
    }

    public function getGlobal($varname = null) {
        if (isset($varname)) {
            if (isset(self::$global_tpl_vars[$varname])) {
                return self::$global_tpl_vars[$varname]->value;
            } else {
                return '';
            }
        } else {
            $_result = array();
            foreach (self::$global_tpl_vars AS $key => $var) {
                $_result[$key] = $var->value;
            }
            return $_result;
        }
    }

    function clearAllCache($exp_time = null, $type = null) {
        $_cache_resource = Smarty_CacheResource::load($this, $type);
        Smarty_CacheResource::invalidLoadedCache($this);
        return $_cache_resource->clearAll($this, $exp_time);
    }

    public function clearCache($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null) {
        $_cache_resource = Smarty_CacheResource::load($this, $type);
        Smarty_CacheResource::invalidLoadedCache($this);
        return $_cache_resource->clear($this, $template_name, $cache_id, $compile_id, $exp_time);
    }


    public function enableSecurity($security_class = null) {
        if ($security_class instanceof Smarty_Security) {
            $this->security_policy = $security_class;
            return $this;
        } elseif (is_object($security_class)) {
            throw new SmartyException("Class '" . get_class($security_class) . "' must extend Smarty_Security.");
        }
        if ($security_class == null) {
            $security_class = $this->security_class;
        }
        if (!class_exists($security_class)) {
            throw new SmartyException("Security class '$security_class' is not defined");
        } elseif ($security_class !== 'Smarty_Security' && !is_subclass_of($security_class, 'Smarty_Security')) {
            throw new SmartyException("Class '$security_class' must extend Smarty_Security.");
        } else {
            $this->security_policy = new $security_class($this);
        }

        return $this;
    }


    public function disableSecurity() {
        $this->security_policy = null;

        return $this;
    }


    public function setTemplateDir($template_dir) {
        $this->template_dir = array();
        foreach ((array) $template_dir as $k => $v) {
            $this->template_dir[$k] = rtrim($v, '/\\') . DS;
        }

        $this->joined_template_dir = join(DIRECTORY_SEPARATOR, $this->template_dir);
        return $this;
    }


    public function addTemplateDir($template_dir, $key=null) {

        $this->template_dir = (array) $this->template_dir;

        if (is_array($template_dir)) {
            foreach ($template_dir as $k => $v) {
                if (is_int($k)) {

                    $this->template_dir[] = rtrim($v, '/\\') . DS;
                } else {

                    $this->template_dir[$k] = rtrim($v, '/\\') . DS;
                }
            }
        } elseif ($key !== null) {

            $this->template_dir[$key] = rtrim($template_dir, '/\\') . DS;
        } else {

            $this->template_dir[] = rtrim($template_dir, '/\\') . DS;
        }
        $this->joined_template_dir = join(DIRECTORY_SEPARATOR, $this->template_dir);
        return $this;
    }


    public function getTemplateDir($index=null) {
        if ($index !== null) {
            return isset($this->template_dir[$index]) ? $this->template_dir[$index] : null;
        }

        return (array)$this->template_dir;
    }


    public function setConfigDir($config_dir) {
        $this->config_dir = array();
        foreach ((array) $config_dir as $k => $v) {
            $this->config_dir[$k] = rtrim($v, '/\\') . DS;
        }

        $this->joined_config_dir = join(DIRECTORY_SEPARATOR, $this->config_dir);
        return $this;
    }


    public function addConfigDir($config_dir, $key=null) {
        $this->config_dir = (array) $this->config_dir;

        if (is_array($config_dir)) {
            foreach ($config_dir as $k => $v) {
                if (is_int($k)) {
                    $this->config_dir[] = rtrim($v, '/\\') . DS;
                } else {
                    $this->config_dir[$k] = rtrim($v, '/\\') . DS;
                }
            }
        } elseif( $key !== null ) {
            $this->config_dir[$key] = rtrim($config_dir, '/\\') . DS;
        } else {
            $this->config_dir[] = rtrim($config_dir, '/\\') . DS;
        }

        $this->joined_config_dir = join(DIRECTORY_SEPARATOR, $this->config_dir);
        return $this;
    }

    public function getConfigDir($index=null) {
        if ($index !== null) {
            return isset($this->config_dir[$index]) ? $this->config_dir[$index] : null;
        }

        return (array)$this->config_dir;
    }

    public function setPluginsDir($plugins_dir) {
        $this->plugins_dir = array();
        foreach ((array)$plugins_dir as $k => $v) {
            $this->plugins_dir[$k] = rtrim($v, '/\\') . DS;
        }

        return $this;
    }

    public function addPluginsDir($plugins_dir) {

        $this->plugins_dir = (array) $this->plugins_dir;

        if (is_array($plugins_dir)) {
            foreach ($plugins_dir as $k => $v) {
                if (is_int($k)) {

                    $this->plugins_dir[] = rtrim($v, '/\\') . DS;
                } else {

                    $this->plugins_dir[$k] = rtrim($v, '/\\') . DS;
                }
            }
        } else {

            $this->plugins_dir[] = rtrim($plugins_dir, '/\\') . DS;
        }

        $this->plugins_dir = array_unique($this->plugins_dir);
        return $this;
    }


    public function getPluginsDir() {
        return (array)$this->plugins_dir;
    }


    public function setCompileDir($compile_dir) {
        $this->compile_dir = rtrim($compile_dir, '/\\') . DS;
        if (!isset(Smarty::$_muted_directories[$this->compile_dir])) {
            Smarty::$_muted_directories[$this->compile_dir] = null;
        }
        return $this;
    }


    public function getCompileDir() {
        return $this->compile_dir;
    }


    public function setCacheDir($cache_dir) {
        $this->cache_dir = rtrim($cache_dir, '/\\') . DS;
        if (!isset(Smarty::$_muted_directories[$this->cache_dir])) {
            Smarty::$_muted_directories[$this->cache_dir] = null;
        }
        return $this;
    }


    public function getCacheDir() {
        return $this->cache_dir;
    }


    public function setDefaultModifiers($modifiers) {
        $this->default_modifiers = (array) $modifiers;
        return $this;
    }


    public function addDefaultModifiers($modifiers) {
        if (is_array($modifiers)) {
            $this->default_modifiers = array_merge($this->default_modifiers, $modifiers);
        } else {
            $this->default_modifiers[] = $modifiers;
        }

        return $this;
    }


    public function getDefaultModifiers() {
        return $this->default_modifiers;
    }



    public function setAutoloadFilters($filters, $type=null) {
        if ($type !== null) {
            $this->autoload_filters[$type] = (array) $filters;
        } else {
            $this->autoload_filters = (array) $filters;
        }

        return $this;
    }


    public function addAutoloadFilters($filters, $type=null) {
        if ($type !== null) {
            if (!empty($this->autoload_filters[$type])) {
                $this->autoload_filters[$type] = array_merge($this->autoload_filters[$type], (array) $filters);
            } else {
                $this->autoload_filters[$type] = (array) $filters;
            }
        } else {
            foreach ((array) $filters as $key => $value) {
                if (!empty($this->autoload_filters[$key])) {
                    $this->autoload_filters[$key] = array_merge($this->autoload_filters[$key], (array) $value);
                } else {
                    $this->autoload_filters[$key] = (array) $value;
                }
            }
        }

        return $this;
    }


    public function getAutoloadFilters($type=null) {
        if ($type !== null) {
            return isset($this->autoload_filters[$type]) ? $this->autoload_filters[$type] : array();
        }

        return $this->autoload_filters;
    }


    public function getDebugTemplate() {
        return $this->debug_tpl;
    }


    public function setDebugTemplate($tpl_name) {
        if (!is_readable($tpl_name)) {
            throw new SmartyException("Unknown file '{$tpl_name}'");
        }
        $this->debug_tpl = $tpl_name;

        return $this;
    }


    public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true) {
        if (!empty($cache_id) && (is_object($cache_id) || is_array($cache_id))) {
            $parent = $cache_id;
            $cache_id = null;
        }
        if (!empty($parent) && is_array($parent)) {
            $data = $parent;
            $parent = null;
        } else {
            $data = null;
        }

        $cache_id = $cache_id === null ? $this->cache_id : $cache_id;
        $compile_id = $compile_id === null ? $this->compile_id : $compile_id;

        if ($this->allow_ambiguous_resources) {
            $_templateId = Smarty_Resource::getUniqueTemplateName($this, $template) . $cache_id . $compile_id;
        } else {
            $_templateId = $this->joined_template_dir . '#' . $template . $cache_id . $compile_id;
        }
        if (isset($_templateId[150])) {
            $_templateId = sha1($_templateId);
        }
        if ($do_clone) {
            if (isset($this->template_objects[$_templateId])) {

                $tpl = clone $this->template_objects[$_templateId];
                $tpl->smarty = clone $tpl->smarty;
                $tpl->parent = $parent;
                $tpl->tpl_vars = array();
                $tpl->config_vars = array();
            } else {
                $tpl = new $this->template_class($template, clone $this, $parent, $cache_id, $compile_id);
            }
        } else {
            if (isset($this->template_objects[$_templateId])) {

                $tpl = $this->template_objects[$_templateId];
                $tpl->parent = $parent;
                $tpl->tpl_vars = array();
                $tpl->config_vars = array();
            } else {
                $tpl = new $this->template_class($template, $this, $parent, $cache_id, $compile_id);
            }
        }

        if (!empty($data) && is_array($data)) {

            foreach ($data as $_key => $_val) {
                $tpl->tpl_vars[$_key] = new Smarty_variable($_val);
            }
        }
        return $tpl;
    }



    public function loadPlugin($plugin_name, $check = true) {

        if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) {
            return true;
        }

        $_name_parts = explode('_', $plugin_name, 3);

        if (!isset($_name_parts[2]) || strtolower($_name_parts[0]) !== 'smarty') {
            throw new SmartyException("plugin {$plugin_name} is not a valid name format");
            return false;
        }

        if (strtolower($_name_parts[1]) == 'internal') {
            $file = SMARTY_SYSPLUGINS_DIR . strtolower($plugin_name) . '.php';
            if (file_exists($file)) {
                require_once($file);
                return $file;
            } else {
                return false;
            }
        }

        $_plugin_filename = "{$_name_parts[1]}.{$_name_parts[2]}.php";
        
        $_stream_resolve_include_path = function_exists('stream_resolve_include_path');


        foreach($this->getPluginsDir() as $_plugin_dir) {
            $names = array(
                $_plugin_dir . $_plugin_filename,
                $_plugin_dir . strtolower($_plugin_filename),
            );
            foreach ($names as $file) {
                if (file_exists($file)) {
                    require_once($file);
                    return $file;
                }
                if ($this->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir)) {

                    if ($_stream_resolve_include_path) {
                        $file = stream_resolve_include_path($file);
                    } else {
                        $file = Smarty_Internal_Get_Include_Path::getIncludePath($file);
                    }
                    
                    if ($file !== false) {
                        require_once($file);
                        return $file;
                    }
                }
            }
        }

        return false;
    }


    public function compileAllTemplates($extention = '.tpl', $force_compile = false, $time_limit = 0, $max_errors = null) {
        return Smarty_Internal_Utility::compileAllTemplates($extention, $force_compile, $time_limit, $max_errors, $this);
    }


    public function compileAllConfig($extention = '.conf', $force_compile = false, $time_limit = 0, $max_errors = null) {
        return Smarty_Internal_Utility::compileAllConfig($extention, $force_compile, $time_limit, $max_errors, $this);
    }


    public function clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null) {
        return Smarty_Internal_Utility::clearCompiledTemplate($resource_name, $compile_id, $exp_time, $this);
    }



    public function getTags(Smarty_Internal_Template $template) {
        return Smarty_Internal_Utility::getTags($template);
    }


    public function testInstall(&$errors=null) {
        return Smarty_Internal_Utility::testInstall($this, $errors);
    }


    public static function mutingErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        $_is_muted_directory = false;


        if (!isset(Smarty::$_muted_directories[SMARTY_DIR])) {
            $smarty_dir = realpath(SMARTY_DIR);
            Smarty::$_muted_directories[SMARTY_DIR] = array(
                'file' => $smarty_dir,
                'length' => strlen($smarty_dir),
            );
        }


        foreach (Smarty::$_muted_directories as $key => &$dir) {
            if (!$dir) {

                $file = realpath($key);
                $dir = array(
                    'file' => $file,
                    'length' => strlen($file),
                );
            }
            if (!strncmp($errfile, $dir['file'], $dir['length'])) {
                $_is_muted_directory = true;
                break;
            }
        }


        if (!$_is_muted_directory || ($errno && $errno & error_reporting())) {
            if (Smarty::$_previous_error_handler) {
                return call_user_func(Smarty::$_previous_error_handler, $errno, $errstr, $errfile, $errline, $errcontext);
            } else {
                return false;
            }
        }
    }


    public static function muteExpectedErrors() {

        $error_handler = array('Smarty', 'mutingErrorHandler');
        $previous = set_error_handler($error_handler);


        if ($previous !== $error_handler) {
            Smarty::$_previous_error_handler = $previous;
        }
    }


    public static function unmuteExpectedErrors() {
        restore_error_handler();
    }
}


Smarty::$_IS_WINDOWS = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';


if (Smarty::$_CHARSET !== 'UTF-8') {
    Smarty::$_UTF8_MODIFIER = '';
}


class SmartyException extends Exception {
}


class SmartyCompilerException extends SmartyException  {
}


function smartyAutoload($class) {
    $_class = strtolower($class);
    $_classes = array(
        'smarty_config_source' => true,
        'smarty_config_compiled' => true,
        'smarty_security' => true,
        'smarty_cacheresource' => true,
        'smarty_cacheresource_custom' => true,
        'smarty_cacheresource_keyvaluestore' => true,
        'smarty_resource' => true,
        'smarty_resource_custom' => true,
        'smarty_resource_uncompiled' => true,
        'smarty_resource_recompiled' => true,
    );

    if (!strncmp($_class, 'smarty_internal_', 16) || isset($_classes[$_class])) {
        include SMARTY_SYSPLUGINS_DIR . $_class . '.php';
    }
}