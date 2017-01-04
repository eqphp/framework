<?php

//rely on: logger
final class mail{

    static $exception = array();
    private $socket, $timeout;
    private $host, $port, $user, $password;
    protected $header, $from, $copy;
    public $subject, $body;

    //初始化构造函数
    function __construct($config = array()){
        if (empty($config)) {
            $config = config(null, 'mail');
        }
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->user = $config['user'];
        $this->password = $config['password'];;
        $this->auth = true;
        $this->timeout = 30;
    }

    //构建邮件头信息
    function build_header($to, $title, $type = 'HTML', $from = array(), $copy = ''){
        if (empty($from)) {
            $from = config('from','mail');
        }
        $this->header = 'MIME-Version:1.0' . PHP_EOL;
        if ($type === "HTML") {
            $this->header .= 'Content-Type:text/html; charset=utf-8' . PHP_EOL;
        }
        $this->header .= 'To: ' . $to . PHP_EOL;
        $this->copy = $copy;
        if ($this->copy) {
            $this->header .= 'Cc: ' . $this->copy . PHP_EOL;
        }
        $this->from = $from['address'];
        $this->header .= "From: {$from['name']}<{$this->from}>" . PHP_EOL;
        $this->header .= 'Subject: ' . $title . PHP_EOL;
        $this->header .= 'Date: ' . date('r') . PHP_EOL;
        $this->header .= 'X-Mailer:By EQPHP (version:3.0)' . PHP_EOL;
        list($msec, $sec) = explode(' ', microtime());
        $id = date('YmdHis', $sec) . '.' . ($msec * 1000000) . '.' . $from['name'];
        $this->header .= "Message-ID: <{$id}>" . PHP_EOL;
    }

    //打开socket
    private function socket_open(){
        $this->socket = fsockopen($this->host, $this->port, $error, $message, $this->timeout);
        if ($this->socket && $this->process_response()) {
            return true;
        }
        $message = $this->host . ',' . $message . '(' . $error . ')';
        array_push(self::$exception, 'host connect fail: ' . $message);
    }

    //单一发送
    private function smtp_send($to){
        try {
            $this->smtp_put_cmd('HELO', $this->host);
            $this->smtp_put_cmd('AUTH LOGIN', base64_encode($this->user));
            $this->smtp_put_cmd('', base64_encode($this->password));
            $this->smtp_put_cmd('MAIL', "FROM:<{$this->from}>");
            $this->smtp_put_cmd('RCPT', "TO:<{$to}>");
            $this->smtp_put_cmd('DATA');
            fwrite($this->socket, $this->header . PHP_EOL . $this->body);
            fwrite($this->socket, PHP_EOL . '.' . PHP_EOL);
            $this->smtp_put_cmd('QUIT');
        } catch (Exception $e) {
            array_push(self::$exception, $e->getMessage());
        }
    }

    //处理put_cmd
    private function smtp_put_cmd($cmd, $arg = ''){
        $arg and $cmd = $cmd ? $cmd . ' ' . $arg : $arg;
        fwrite($this->socket, $cmd . "\r\n");
        if ($this->process_response()) {
            return true;
        }
        array_push(self::$exception, $cmd . ' command send fail');
    }

    //处理smtp
    private function process_response(){
        $response = str_replace(PHP_EOL, '', fgets($this->socket, 512));
        if (preg_match('/^[23]/', $response)) {
            return true;
        }
        fwrite($this->socket, 'QUIT' . PHP_EOL);
        fgets($this->socket, 512);
        array_push(self::$exception, 'remote host response: ' . $response);
    }

    //发送邮件
    function send($to, $copy = '', $from = array(), $type = 'HTML'){
        $this->build_header($to, $this->subject, $type, $from, $copy);
        $this->body = preg_replace("/(^|(\r\n))(\\.)/", "\\1.\\3", $this->body);
         if ($this->copy) {
            $to .= ';' . $this->copy;
        }
        $receive_list = explode(';', trim($to, ';'));
        foreach ($receive_list as $receive) {
            if ($this->socket_open()) {
                $this->smtp_send($receive);
            }
        }
        if (self::$exception) {
            logger::exception('mail', implode(PHP_EOL, self::$exception));
            throw new Exception('part mail send fail', 109);
        }
        return true;
    }

    //解析邮件模版
    function take(array $data, $template = ''){
        if ($template) {
            $dom = file_get_contents(VIEW_TEMPLATE . $template . '.html');
            foreach ($data as $key => $value) {
                $dom = str_replace('{' . $key . '}', $value, $dom);
            }
            $regular = '/<title>(.*)<\/title>[\w\W]*<body>(.*)<\/body>/isU';
            preg_match($regular, $dom, $option);
            $this->subject = $this->body = '';
            if (count($option) == 3) {
                $this->subject = $option[1];
                $this->body = $option[2];
            }
        } else {
            list($this->subject, $this->body) = $data;
        }
        return $this;
    }


}