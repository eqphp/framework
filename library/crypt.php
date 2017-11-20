<?php

class crypt{

    const PREFIX = '_-*-_';
    const METHOD = 'AES-128-ECB';

    function __construct($secret_key = 'b335a4503870a1d1'){
        $this->secret_key = $secret_key;
    }

    //加密
    function encrypt($string, $secret_key = '', $is_verify = false){
        if ($is_verify) {
            $buffer = '';
            $length = mb_strlen($string);
            for ($i = 0; $i < $length; $i++) {
                $string = mb_substr($string, $i, 1, 'utf8');
                $buffer .= base64_encode(openssl_encrypt($string, self::METHOD, $secret_key, OPENSSL_RAW_DATA));
            }
            return $buffer;
        }
        if (strlen(self::PREFIX) && strpos($string, self::PREFIX) !== 0) {
            $secret_key = $secret_key ? $secret_key : $this->secret_key;
            $string = openssl_encrypt($string, self::METHOD, $secret_key, OPENSSL_RAW_DATA);
            return self::PREFIX . base64_encode($string);
        }
        return strlen($string) ? $string : '';
    }

    //解密
    function decrypt($string, $secret_key = ''){
        if (strlen(self::PREFIX) && strpos($string, self::PREFIX) === 0) {
            $secret_key = $secret_key ? $secret_key : $this->secret_key;
            $string = base64_decode(str_replace(self::PREFIX, '', $string));
            return openssl_decrypt($string, self::METHOD, $secret_key, OPENSSL_RAW_DATA);
        }
        return strlen($string) ? $string : '';
    }


}