<?php

class a_restful{

    //接口版本号
    const API_VERSION = '1.0.0';

    //定义token有效期、密钥
    const TOKEN_EXPIRE = '3 days';
    const TOKEN_KEY = 'db4139a692060d9416ce2b2ca6156490';

    //指定允许的restful方法
    static $allow_method = array('get', 'post', 'put', 'delete', 'head', 'options');

    //请求方法、api版本
    protected $request_method, $request_version;

    //授权token,授权用户ID
    protected $auth_token, $user_id;

    function __construct($is_need_authorized = false){
        //检验请求方法
        $this->request_method = input::server('request_method', 'account');
        if (!in_array($this->request_method, self::$allow_method, true)) {
            $this->response(1, 'restful method not allowed');
        }

        //检测API版本
        $this->request_version = input::server('http_if_match');
        if ($this->request_version !== self::API_VERSION) {
            $this->response(2, 'application version mismatch');
        }

        //处理请求参数simplexml_load_string($request_data);
        $request_data = trim(file_get_contents('php://input'));
        if (preg_match('/^\{.*[\w\W]*\}$/', $request_data)) {
            $_POST = json_decode($request_data, true);
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

        $this->{$this->request_method}();
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
        $this->response(5, 'undefined request method');
    }

}