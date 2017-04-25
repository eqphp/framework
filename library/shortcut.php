<?php

//use eqphp\system, eqphp\session, eqphp\input, eqphp\query, eqphp\smarty3, eqphp\debug;

define('PATH_CONFIG', PATH_ROOT . 'config/');
define('PATH_LIBRARY', PATH_ROOT . 'library/');
define('PATH_MODULE', PATH_ROOT . 'module/');
define('PATH_SERVER', PATH_ROOT . 'server/');
define('PATH_FILE', PATH_ROOT . 'file/');
define('PATH_VIEW', PATH_ROOT . 'view/');
define('PATH_DATA', PATH_ROOT . 'data/');
define('PATH_CACHE', PATH_ROOT . 'cache/');
define('PATH_LOG', PATH_ROOT . 'log/');
define('PATH_VENDOR', PATH_ROOT . 'vendor/');

define('FILE_PICTURE', PATH_FILE . 'picture/');
define('FILE_TEMP', PATH_FILE . 'temp/');
define('FILE_AVATAR', PATH_FILE . 'picture/avatar/');
define('FILE_MEDIA', PATH_FILE . 'media/');
define('FILE_DOC', PATH_FILE . 'document/');
define('FILE_ZIP', PATH_FILE . 'package/');
define('FILE_HTML', PATH_FILE . 'html/');
define('FILE_JSON', PATH_FILE . 'json/');
define('FILE_EDITOR', PATH_FILE . 'editor/');
define('FILE_STATIC', PATH_FILE . 'static/');
define('FILE_SCRIPT', PATH_FILE . 'static/script/');
define('FILE_STYLE', PATH_FILE . 'static/style/');
define('FILE_IMAGE', PATH_FILE . 'static/image/');

define('DATA_LANG', PATH_DATA . 'lang/');
define('DATA_DICT', PATH_DATA . 'dict/');
define('DATA_FONT', PATH_DATA . 'font/');
define('DATA_STORE', PATH_DATA . 'store/');
define('DATA_BACKUP', PATH_DATA . 'backup/');
define('DATA_SOURCE', PATH_DATA . 'source/');

define('LOG_RUN', PATH_LOG . 'run/');
define('LOG_TEST', PATH_LOG . 'test/');
define('LOG_TRACE', PATH_LOG . 'trace/');
define('LOG_TOPIC', PATH_LOG . 'topic/');
define('LOG_MYSQL', PATH_LOG . 'mysql/');
define('LOG_MONGO', PATH_LOG . 'mongo/');
define('LOG_VISIT', PATH_LOG . 'visit/');

define('URL_FILE', 'http://www.q.com/');
define('URL_PICTURE', URL_FILE . 'file/picture/');
define('URL_TEMP', URL_FILE . 'file/temp/');
define('URL_AVATAR', URL_FILE . 'file/picture/avatar/');
define('URL_MEDIA', URL_FILE . 'file/media/');
define('URL_DOC', URL_FILE . 'file/document/');
define('URL_ZIP', URL_FILE . 'file/package/');
define('URL_HTML', URL_FILE . 'file/html/');
define('URL_JSON', URL_FILE . 'file/json/');
define('URL_EDITOR', URL_FILE . 'file/editor/');
define('URL_STATIC', URL_FILE . 'file/static/');
define('URL_SCRIPT', URL_FILE . 'file/static/script/');
define('URL_STYLE', URL_FILE . 'file/static/style/');
define('URL_IMAGE', URL_FILE . 'file/static/image/');

//解析分组配置文件
function config($name, $is_module = false){
    return system::config($name, $is_module);
}

//会话
function session($key = null, $value = false){
    return session::merge($key, $value);
}

//对象工厂
function with(){
    return call_user_func_array('basic::with', func_get_args());
}

//获取url参数
function url($lie, $type = 0){
    return input::url($lie, $type);
}

//表单值接收处理
function post($name, $mode = 'title'){
    return input::post($name, $mode);
}

//实例化数据表模型
function query($table){
    return new query($table);
}

//模板配置
function smarty(){
    return smarty3::tpl();
}

//调试
function out($param = 'ok', $mode = 1, $is_exit = true){
    debug::out($param, $mode, $is_exit);
}