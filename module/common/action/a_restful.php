<?php

class a_restful{

    //接口版本号
    const API_VERSION = '1.0.0';

    //定义token有效期、密钥
    const TOKEN_EXPIRE = '30 days';
    const TOKEN_KEY = 'db4139a692060d9416ce2b2ca6156490';

    //指定允许的restful方法
    static $allow_method = array('get', 'post', 'put', 'patch', 'delete', 'head', 'options');

    //请求方法、api版本、响应数据
    protected $request_method, $request_version, $data;

    //授权token,授权用户ID
    protected $auth_token, $user_id;

    //分页
    protected $page = 1, $page_size = 20;


    function __construct($is_need_authorized = true){
        //检验请求方法
        $this->request_method = strtolower(input::server('request_method', 'title'));
        if (!in_array($this->request_method, self::$allow_method, true)) {
            $this->response(1, 'restful method not allowed');
        }

        //检测API最低版本
        $this->request_version = input::server('http_if_match');
        if (version_compare($this->request_version, self::API_VERSION, 'lt')) {
            $this->response(2, 'application version mismatch');
        }

        //分页信息
        $range = input::server('http_range');
        if (strpos($range, '/')) {
            $range = explode('/', $range);
            $this->page = max(1, $range[0] + 0);
            $this->page_size = max(1, $range[1] + 0);
        }

        //处理请求参数simplexml_load_string($request_data);
        $request_data = trim(file_get_contents('php://input'));
        if (preg_match('/^\{.*[\w\W]*\}$/', $request_data)) {
            $_POST = json_decode($request_data, true);
        }
        if (in_array($this->request_method, array('put', 'patch'), true)) {
            parse_str($request_data, $_POST);
        }

        //用户授权
        if ($is_need_authorized) {
            $this->auth_token = input::server('HTTP_TOKEN');
            if ($this->auth_token) {
                $this->user_id = $this->check_token_expire();
            }
            if (empty($this->user_id)) {
                $this->response(3, 'unauthorized');
            }
        }
    }

    //检查token是否失效
    private function check_token_expire(){
        $crypt = new crypt(self::TOKEN_KEY);
        $token = $crypt->decrypt(base64_decode(trim($this->auth_token)));
        if (substr_count($token, '|') !== 1) {
            $this->response(4, 'token expire');
        }
        list($user_id, $time) = explode('|', $token);
        if ($time < time()) {
            $this->response(4, 'token expire');
        }
        return $user_id + 0;
    }

    //获取指定用户的token
    protected function get_token($user_id){
        $crypt = new crypt(self::TOKEN_KEY);
        $plain_text = $user_id . '|' . strtotime(self::TOKEN_EXPIRE);
        return base64_encode($crypt->encrypt($plain_text));
    }

    //异步json简易提示
    protected function response($error = 0, $message = 'ok', $data = null){
        http::json(compact('error', 'message', 'data'));
    }

    function __call($name, $param = null){
        if (method_exists($this, $this->request_method)) {
            $this->{$this->request_method}();
        } else {
            $this->response(5, 'undefined request method');
        }
    }

}