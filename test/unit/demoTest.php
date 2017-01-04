<?php

//声明测试模块
define('MODULE_NAME', 'user');
include 'baseTest.php';

class demoTest extends PHPUnit_Framework_TestCase{

    /**
     * @dataProvider additionProvider
     */
    function testTool($phone,$result){
        //工具类
        $this->assertEquals($result, regexp::match($phone,'phone'));
    }

    //业务类
    function testServer(){
        $point = point('common', 2, 'register')->execute();
        $this->assertEquals(50, $point);
    }

    //模型
    function testModel(){
        $user = m_login::login(array('qq' => '2581221391'), md5('123456'));
        $this->assertEquals(2, $user['id']);
    }

    public function additionProvider(){
        return [
            'a' => ['15001329580', true],
            'b' => ['15202921877', true],
            'c' => ['15847586321', true],
            'd' => ['19985410142', true],
        ];
    }

}