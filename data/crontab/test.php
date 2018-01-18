<?php

include 'crontab.php';


//$start = input::get('start', 'int');
//$category = input::get('category', 'title');
//out([$start, $category]);


class finance{

    const PARTNER_URL = 'https://###';

    protected $data = [];

    function __construct(){
        //$this->data = with('current_finance_task', 'pending')->get(100);
        //……
    }

    static function receivable(){
        //TODO
    }

    public function refund(){
        $option = ['type' => 'json', 'xml_tag' => '', 'is_array' => true];
        $data = ['refund_code' => 'AD_699', 'money' => 15000, 'request_time' => time()];
        http::curl(self::PARTNER_URL . 'refund', $data, null, $option);
    }

    public function notify(){
        echo 'ok';
        http::curl(self::PARTNER_URL . 'notify', 'thanks clear finance for eqphp');
    }

}
