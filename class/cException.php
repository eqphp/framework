<?PHP

class cException extends Exception{

    public $error;

    //code: 10~99:module,500~999:exception code
    function __construct($message, $code, $error = 0){
        logger::exception('action', $code . ' : ' . $message);
        $this->error = $error;
        parent::__construct($message, $code);
    }

    function __get($name){
        return $this->error;
    }

    function __set($name, $value){
        if ($name === 'error') {
            $this->error += $value;
        }
        return $this->error;
    }


}