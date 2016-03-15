<?php

//rely on: logger
final class mail{

    static $exception = array();
    private static $object = null;
    private $sock, $host, $port, $mail, $password, $time_out;

    //初始化构造函数
    function __construct($host, $port, $mail, $password, $auth = false){
        $this->host = $host;
        $this->port = $port;
        $this->mail = $mail;
        $this->password = $password;
        $this->auth = $auth;
        $this->time_out = 30;
    }

    //发送邮件
    private function send_mails($to, $title, $content, $type = 'HTML', $from = array(), $cc = ''){
        list($header, $from_address, $cc) = self::process_header($to, $title, $type, $from, $cc);
        $content = preg_replace("/(^|(\r\n))(\\.)/", "\\1.\\3", $content);
        if (strpos($to, ',') === false && empty($cc)) {
            return $this->send_mail($to, $from_address, $header, $content);
        }
        $receive_list = explode(',', self::act_address_list($to));
        $cc and $receive_list = array_merge($receive_list, explode(',', $cc));
        foreach ($receive_list as $receive) {
            $this->send_mail($receive, $from_address, $header, $content);
        }
    }

    //拼接邮件头信息
    static function process_header($to, $title, $type = 'HTML', $from = array(), $cc = ''){
        $header = "MIME-Version:1.0\r\n";
        if ($type === "HTML") {
            $header .= "Content-Type:text/html; charset=utf-8\r\n";
        }
        $header .= "To: " . $to . "\r\n";
        if ($cc = self::act_address_list($cc)) {
            $header .= "Cc: " . $cc . "\r\n";
        }
        $from['address'] = self::act_address(self::act_address_list($from['address']));
        $header .= 'From: ' . $from['name'] . '<' . $from['address'] . ">\r\n";
        $header .= "Subject: " . $title . "\r\n";
        $header .= "Date: " . date("r") . "\r\n";
        $header .= 'X-Mailer:By EQPHP (version:3.0)' . "\r\n";
        list($msec, $sec) = explode(' ', microtime());
        $header .= 'Message-ID: <' . date('YmdHis', $sec) . '.' . ($msec * 1000000) . '.' . $from['name'] . ">\r\n";
        return array($header, $from['address'], $cc);
    }

    //发送单封邮件
    private function send_mail($receive, $from_address, $header, $content){
        if ($this->smtp_sockopen()) {
            $receive = self::act_address($receive);
            if ($this->smtp_send($this->host, $from_address, $receive, $header, $content)) {
                if (config('log.is_record_mail', 'mail')) {
                    logger::mail($receive);
                }
                return true;
            }
        }
        array_push(self::$exception, 'cannot send email to ' . $receive);
    }

    //执行socket
    private function smtp_sockopen(){
        $this->sock = fsockopen($this->host, $this->port, $error_code, $error_message, $this->time_out);
        if ($this->sock && $this->act_smtp()) {
            return true;
        }
        $message = $this->host . ',' . $error_message . '(' . $error_code . ')';
        array_push(self::$exception, "can't connect to relay host: " . $message);
        return false;
    }

    //内核单一发送
    private function smtp_send($host, $from, $to, $header, $content = ''){
        try {
            $this->smtp_put_cmd("HELO", $host);
            $this->smtp_put_cmd("AUTH LOGIN", base64_encode($this->mail));
            $this->smtp_put_cmd("", base64_encode($this->password));
            $this->smtp_put_cmd("MAIL", "FROM:<" . $from . ">");
            $this->smtp_put_cmd("RCPT", "TO:<" . $to . ">");
            $this->smtp_put_cmd("DATA");
            fwrite($this->sock, $header . "\r\n" . $content);
            fwrite($this->sock, "\r\n.\r\n");
            $this->smtp_put_cmd('QUIT');
            return true;
        } catch (Exception $e) {
            array_push(self::$exception, $e->getMessage());
            return false;
        }
    }

    //处理put_cmd
    private function smtp_put_cmd($cmd, $arg = ''){
        $arg and $cmd = $cmd ? $cmd . ' ' . $arg : $arg;
        fwrite($this->sock, $cmd . "\r\n");
        if ($this->act_smtp()) {
            return true;
        }
        throw new Exception('sending ' . $cmd . ' command fail', 109);
    }

    //处理smtp
    private function act_smtp(){
        $response = str_replace("\r\n", "", fgets($this->sock, 512));
        if (preg_match("/^[23]/", $response)) {
            return true;
        }
        fwrite($this->sock, "QUIT\r\n");
        fgets($this->sock, 512);
        array_push(self::$exception, 'remote host returned: ' . $response);
        return false;
    }

    //处理单个邮箱
    static function act_address($address){
        $address = preg_replace("/([ \t\r\n])+/", "", $address);
        return preg_replace("/^.*<(.+)>.*$/", "\\1", $address);
    }

    //处理邮箱列表
    static function act_address_list($address){
        while (preg_match("/\\([^()]*\\)/", $address)) {
            $address = preg_replace("/\\([^()]*\\)/", '', $address);
        }
        return $address;
    }

    //解析配置，返回mail对象
    static function get_instance($mail = null){
        if (self::$object instanceof self) {
            return self::$object;
        }
        if (is_null($mail)) {
            $mail = config(null, 'mail');
        }
        self::$object = new self($mail['host'], $mail['port'], $mail['user'], $mail['password'], true);
        return self::$object;
    }

    //发送邮件
    static function send($to, $title, $content, $cc = '', $type = 'HTML'){
        $mail = self::get_instance();
        $from = config('from', 'mail');
        $mail->send_mails($to, $title, $content, $type, $from, $cc);
        if (self::$exception) {
            logger::exception('mail', implode(PHP_EOL, self::$exception));
        }
    }

    //解析邮件模版
    static function tpl($tpl, $data, &$title, &$content){
        $html = file_get_contents(VIEW_TEMPLATE . $tpl . '.html');
        $html = preg_replace($data[0], $data[1], $html);
        $regular = '/<title>(.*)<\/title>[\w\W]*<body>(.*)<\/body>/isU';
        preg_match($regular, $html, $option);
        if (count($option) == 3) {
            list($title, $content) = array($option[1], $option[2]);
        }
        return true;
    }

}