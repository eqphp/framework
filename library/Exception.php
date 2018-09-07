<?PHP
namespace eqphp;


class Exception extends \Exception{

    public $error;

    //server(500~999)
    //model(10~99:module,001~499:exception code)
    //action(10~99:module,500~999:exception code)
    function __construct($message, $code, $error = 0){
        $this->error = $error;
        parent::__construct($message, $code);
        logger::exception('server', $code . ' : ' . $message);
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