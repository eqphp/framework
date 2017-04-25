<?php

class crypt{

    const TYPE = MCRYPT_RIJNDAEL_128;
    const MODE = MCRYPT_MODE_ECB;
    const METHOD = 'b335a4503870a1d1';
    protected $td, $iv = '';

    function __construct($secret_key = 'b335a4503870a1d1'){
        $this->secret_key = $secret_key;
    }

    protected function process($string, $action){
        if (self::METHOD) {
            $callback = __CLASS__ . '::' . self::METHOD . '_' . $action;
            if (is_callable($callback)) {
                $size = mcrypt_get_block_size(self::TYPE, self::MODE);
                return call_user_func($callback, $string, $size);
            }
        }
        return $string;
    }

    static function b335a4503870a1d1_fill($text, $size){
        $pad = $size - (strlen($text) % $size);
        return $text . str_repeat(chr($pad), $pad);
    }

    static function b335a4503870a1d1_clear($text){
        $pad = ord($text{strlen($text) - 1});
        $compare = strspn($text, chr($pad), strlen($text) - $pad);
        if (strlen($text) < $pad || $compare != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    protected function get_iv(){
        $this->td = mcrypt_module_open(self::TYPE, '', self::MODE, '');
        if (empty($this->iv)) {
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->td), MCRYPT_RAND);
        } else {
            $iv = $this->iv;
        }
        mcrypt_generic_init($this->td, $this->secret_key, $iv);
    }


    public function encrypt($string){
        $string = $this->process($string, 'fill');
        $this->get_iv();
        $crypt_text = mcrypt_generic($this->td, $string);
        $rt = base64_encode($crypt_text);
        mcrypt_generic_deinit($this->td);
        mcrypt_module_close($this->td);
        return $rt;
    }

    public function decrypt($string){
        $this->get_iv();
        $decrypted_text = mdecrypt_generic($this->td, base64_decode($string));
        mcrypt_generic_deinit($this->td);
        mcrypt_module_close($this->td);
        return $this->process($decrypted_text, 'clear');
    }

}