<?php

    class s_aes{

        const TYPE=MCRYPT_RIJNDAEL_128;
        const MODE=MCRYPT_MODE_ECB;
        const PAD_METHOD='pkcs5';
        protected $td,$iv='';

        function __construct($secret_key='#*!@dianfubao%$&'){
            $this->secret_key=$secret_key;
        }

        protected function pad_or_unpad($str,$ext){
            if (self::PAD_METHOD) {
                $func_name=__CLASS__.'::'.self::PAD_METHOD.'_'.$ext.'pad';

                if (is_callable($func_name)) {
                    $size=mcrypt_get_block_size(self::TYPE,self::MODE);
                    return call_user_func($func_name,$str,$size);
                }
            }
            return $str;
        }

        public static function pkcs5_pad($text,$blocksize){
            $pad=$blocksize-(strlen($text)%$blocksize);
            return $text.str_repeat(chr($pad),$pad);
        }

        public static function pkcs5_unpad($text){
            $pad=ord($text{strlen($text)-1});
            $compare=strspn($text,chr($pad),strlen($text)-$pad);
            if (strlen($text) < $pad || $compare != $pad) {
                return false;
            }
            return substr($text,0,-1*$pad);
        }

        protected function get_iv(){
            $this->td=mcrypt_module_open(self::TYPE,'',self::MODE,'');
            if (empty($this->iv)) {
                $iv=mcrypt_create_iv(mcrypt_enc_get_iv_size($this->td),MCRYPT_RAND);
            } else {
                $iv=$this->iv;
            }
            mcrypt_generic_init($this->td,$this->secret_key,$iv);
        }


        public function encrypt($str){
            $str=$this->pad_or_unpad($str,'');
            $this->get_iv();
            $cyper_text=mcrypt_generic($this->td,$str);
            $rt=base64_encode($cyper_text);
            mcrypt_generic_deinit($this->td);
            mcrypt_module_close($this->td);
            return $rt;
        }

        public function decrypt($str){
            $this->get_iv();
            $decrypted_text=mdecrypt_generic($this->td,base64_decode($str));
            $rt=$decrypted_text;
            mcrypt_generic_deinit($this->td);
            mcrypt_module_close($this->td);
            return $this->pad_or_unpad($rt,'un');
        }

    }