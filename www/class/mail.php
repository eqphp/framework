<?php

final class mail{

    private $host;
    private $port;
    private $mail;
    private $pwd;
    public $sock;
    private $time_out;


    //初始化构造函数
    function __construct($host,$port,$mail,$pwd,$auth=false){
        $this->host=$host;
        $this->port=$port;
        $this->mail=$mail;
        $this->pwd=$pwd;
        $this->auth=$auth;
        $this->time_out=30;
    }

    //发送邮件
    private function send_mail($to,$title,$content,$type='HTML',$from='',$cc=''){
        $content=preg_replace("/(^|(\r\n))(\\.)/","\\1.\\3",$content);
        $from=self::act_address_list($from);
        $from=self::act_address($from);

        $header="MIME-Version:1.0\r\n";
        if ($type == "HTML") $header.="Content-Type:text/html\r\n";
        $header.="To: ".$to."\r\n";
        if ($cc) $header.="Cc: ".$cc."\r\n";

        $header.='From: '.$from.'<'.$from.">\r\n";
        $header.="Subject: ".$title."\r\n";
        $header.="Date: ".date("r")."\r\n";
        $header.='X-Mailer:By Redhat (PHP/'.phpversion().")\r\n";

        list($msec,$sec)=explode(' ',microtime());
        $header.='Message-ID: <'.date('YmdHis',$sec).'.'.($msec*1000000).'.'.$from.">\r\n";

        $to_arr=explode(',',self::act_address_list($to));

        if ($cc) {
            $to_arr=array_merge($to_arr,explode(',',self::act_address_list($cc)));
        }

        $send_result=true;
        foreach ($to_arr as $addressee) {
            $addressee=self::act_address($addressee);
            if (!$this->smtp_sockopen($addressee)) {
                log::exception('mail','cannot send email to '.$addressee);
                $send_result=false;
                continue;
            }

            $send_result=$this->smtp_send($this->host,$from,$addressee,$header,$content);

            if (config('log|mail','log')) {
                log::mail($addressee); //记录发送成功的邮箱
            }

            if (!$send_result) {
                log::exception('mail','cannot send email to '.$addressee);
            }
        }

        return $send_result;
    }

    //内核单一发送
    function smtp_send($host,$from,$to,$header,$content=''){
        if (!$this->smtp_put_cmd("HELO",$host)) {
            log::exception('mail','sending HELO command');
            return false;
        }

        if ($this->auth) {
            if (!$this->smtp_put_cmd("AUTH LOGIN",base64_encode($this->mail))) {
                log::exception('mail','sending HELO command');
                return false;
            }

            if (!$this->smtp_put_cmd("",base64_encode($this->pwd))) {
                log::exception('mail','sending HELO command');
                return false;
            }
        }

        if (!$this->smtp_put_cmd("MAIL","FROM:<".$from.">")) {
            log::exception('mail','sending MAIL FROM command');
            return false;
        }

        if (!$this->smtp_put_cmd("RCPT","TO:<".$to.">")) {
            log::exception('mail','sending RCPT TO command');
            return false;
        }

        if (!$this->smtp_put_cmd("DATA")) {
            log::exception('mail','sending DATA command');
            return false;
        }

        fwrite($this->sock,$header."\r\n".$content);

        fwrite($this->sock,"\r\n.\r\n");
        if (!$this->act_smtp()) {
            log::exception('mail','sending <CR><LF>.<CR><LF> [EOM]');
            return false;
        }

        if (!$this->smtp_put_cmd("QUIT")) {
            log::exception('mail','sending QUIT command');
            return false;
        }

        return true;
    }

    //执行socket
    private function smtp_sockopen($address){
        if ($this->host == '') {
            return $this->smtp_sockopen_mx($address,$this->port);
        }
        return $this->smtp_sockopen_relay($this->port);
    }


    //执行socket_mx
    private function smtp_sockopen_mx($address,$port){
        $domain=@ereg_replace("^.+@([^@]+)$","\\1",$address);
        if (!getmxrr($domain,$MXHOSTS)) {
            log::exception('mail','Cannot resolve MX-'.$domain);
            return false;
        }
        foreach ($MXHOSTS as $host) {
            log::exception('mail','trying to '.$host.':'.$port);

            $this->sock=fsockopen($host,$port,$errno,$errstr,$this->time_out);
            if (!($this->sock && $this->act_smtp())) {

                log::exception('mail','warning: cannot connect to mx host '.$host);
                log::exception('mail',$errstr.' ('.$errno.')');

                continue;
            }
            log::exception('mail','connected 0 to mx host '.$host);
            return true;
        }

        log::exception('mail','cannot connect to any mx hosts ('.implode(", ",$MXHOSTS).')');
        return false;
    }


    //执行socket_relay
    private function smtp_sockopen_relay($port){
        $this->sock=fsockopen($this->host,$port,$errno,$errstr,$this->time_out);
        if (!($this->sock && $this->act_smtp())) {
            log::exception('mail','cannot connenct to relay host '.$this->host);
            log::exception('mail',$errstr.' ('.$errno.')');
            return false;
        }
        return true;
    }

    //处理put_cmd
    private function smtp_put_cmd($cmd,$arg=''){
        if ($arg) $cmd=($cmd == '') ? $arg : $cmd.' '.$arg;
        fwrite($this->sock,$cmd."\r\n");
        return $this->act_smtp();
    }

    //处理smtp
    private function act_smtp(){
        $response=str_replace("\r\n","",fgets($this->sock,512));
        if (!preg_match("/^[23]/",$response)) {
            fwrite($this->sock,"QUIT\r\n");
            fgets($this->sock,512);
            log::exception('mail','remote host returned-'.$response);
            return false;
        }
        return true;
    }

    //处理邮箱列表
    static function act_address_list($address){
        while (preg_match("/\\([^()]*\\)/",$address)) {
            $address=preg_replace("/\\([^()]*\\)/",'',$address);
        }
        return $address;
    }

    //处理单个邮箱
    static function act_address($address){
        $address=preg_replace("/([ \t\r\n])+/","",$address);
        return preg_replace("/^.*<(.+)>.*$/","\\1",$address);
    }

    //解析配置，返回mail对象
    private static function config(){
        $mail=config('mail','mail');
        return new self($mail['host'],$mail['port'],$mail['user'],$mail['pwd'],true);
    }

    //发送邮件
    static function send($to,$title,$content,$type='HTML'){
        $mail=self::config();
        $from=config('mail|from','mail');
        $mail->debug=true;
        return $mail->send_mail($to,$title,$content,$type,$from);
    }

    //解析邮件模版
    static function tpl($tpl,$data,&$title,&$content){
        $info=file_get_contents(dc_view_template.$tpl.'.html');
        $info=preg_replace($data[0],$data[1],$info);
        $regular='/<title>(.*)<\/title>[\w\W]*<body>(.*)<\/body>/isU';
        preg_match($regular,$info,$option);
        if (count($option) == 3) {
            list($title,$content)=array($option[1],$option[2]);
            return true;
        }
    }

    //查询mail类方法
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、解析模版：tpl($tpl,$data,&$title,&$content)<br>';
        $info.='2、发送邮件：send($to,$title,$content,$type="HTML")</font><br><br>';
        return $info;
    }

}