<?PHP

class mException extends Exception{

    public $error;

    //code: 10~99:module,001~499:exception code
    function __construct($message, $code, $error = 0){
        $this->error = $error;
        parent::__construct($message, $code);
        logger::exception('model', $code . ' : ' . $message);
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