<?php

class a_index{

    //静态类(yes)
    private $static_class;

	//首页
    static function index(){

        $head=array('title'=>'EQPHP开源中文WEB应用开发框架');
        input::cookie('frame_name','EQPHP');

        $logo_file=DATA_STORE.'txt/logo_pic.txt';
        $source=base64_decode(file_get_contents($logo_file));
        file_put_contents(FILE_CREATE.'eqphp_logo.png',$source);
        $logo='<img src="'.URL_CREATE.'eqphp_logo.png">';
        smarty()->assign(compact('head','logo'))->display('index');

   }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	





    //首页
    static function index75(){

   


   
   
   
   $mongo=mg::connect();
   
   
   out($mongo);









//        out([1,2.5,7,'aa'],2,0);
        out(debug::info(4));
	
	
	
	
	
	Session::set('Authorization','Bearer 10E7JyjYsqGLm2tTgGSjs2UPA2sBR8NTtJxtho8yBZJVEf4B/ynCN5bvQY39RkMPYFjbI=');
//	$query='?initial=true&isValid=SUBMIT%2CPENDING%2CPENDING_REVIEW&type=company&submit_start_date=2015-07-01+00%3A00%3A01&sort=-startTime';
//	$header=self::get_range(2);

	
	
	// $condition['initial']='true';
//	$condition['isValid']='SUBMIT,PENDING,PENDING_REVIEW';
	// $condition['type']='company';
	// $condition['submit_start_date']='2015-07-01 00:00:01';
//	$condition['sort']='-startTime';
	
	//http://10.10.0.202:9005/updateamount?initial=true&isValid=SUBMIT%2CPENDING%2CPENDING_REVIEW&type=company&submit_start_date=2015-07-01+00%3A00%3A01&sort=-startTime
//		$p=with('api','credit')->get('',$condition,$header)->call();

        $p=with('api','credit')->get('5e0947df-2224-4a57-b35f-8694d80083e5')->call('debug');
	    out($p);
	
	
	
	
	
	
		
	
	// out(md5(md5('123456').'1&cc`5'));

//        out(debug::info(1));


        smarty()->display('index');
        exit;
	// out(s_identity::parse_card('610321198805221547'));
	
	// out(debug::info('const'));

        out(memory::init()->get('name'));//,array('phone'=>'15001329580')));
	
        smarty()->display('index');exit;
        $ru=regexp::match('kill','/^(mark|kill|jim)$/');
        out($ru);


        for ($n=-10; $n < 10; $n++) {
            if (($n+$n+1+$n+2) === ($n*($n+1)*($n+2))) {
                echo $n.HR;
            }
        }

        out(ok);
        $data=array(1,2,3,4);
        out(false or out(array_product($data)));

        out(regexp::get());


        http::abort('密码错误',route('user'),30);

        out(preg_match('/^\-?[0-9]{0,}\.?[0-9]{0,}$/','5.00'));

        out(s_point_rule::get_rule_operation(3));

        out(s_point_rule::modify_rule(2,5,'complete_personal_information','完善个人资料',50,'完善个人资料：积5分，每人仅限一次'));
        out(s_point_rule::add_rule(2,'complete_personal_information','完善个人资料',10,'完善个人资料：积10分，每人仅限一次'));

        out(s_point_rule::change_status(2,array(1,2,3),'run','项目上线,开启所有规则'));

       point('common',8,'register')->execute();

    }

    static function index76($param){
        out(get_cfg_var('environment'));

        get_cfg_var('environment');
        out(s_system::send_message(8,'测试系统消息推送器','test'));

        $option['id']=array('in',array(8,1));
        $amount=db::field(s_user::TABLE_USER,'count(1)',$option);


        out(with('cache','test.php',6,9,0,6,86));
        $data=array(8=>array('id'=>8,'name'=>'lily','age'=>8));

        out(with('cache','test.php')->save($data));

        out(s_user::get_relation(1,8));

        out(file::read('family_name',0));

        out(s_identity::family_name('李世民'));
        $data=array(
            array('id'=>1,'name'=>'jim','age'=>22,'score'=>89),
            array('id'=>2,'name'=>'lily','age'=>25,'score'=>75),
            array('id'=>3,'name'=>'tom','age'=>27,'score'=>92),
            array('id'=>4,'name'=>'jerry','age'=>28,'score'=>89),
        );

        debug::flag($t1);
        $sort=basic::array_field_sort($data,'age,score');
        array_multisort($sort['score'],SORT_DESC,$sort['age'],$data);
        debug::flag($t2);

        out(array($data,debug::used($t1,$t2)));
    }
	
	
	static function api(){
	
	
		//{"checks":{"userEmail":"true"},"userId":"d422e36a-8ae0-45e0-a5b8-4975dbc54650"}
		//{"checks":{"userEmail":"1000000012@qq.com##only"},"userId":"d422e36a-8ae0-45e0-a5b8-4975dbc54650"}
	
//		$data=array('checks'=>array('userEmail'=>'1000000012@qq.com##only'),'userId'=>'d422e36a-8ae0-45e0-a5b8-4975dbc54650');
//		$p=with('api','exist')->post($data)->call();


        //$data=array("direction"=>"out","sourcePhone"=>"18810872022","targetPhone"=>"15810872022");
        //$header=array('Content-Type'=>'application/vnd.kaiyuan.platform.transaction.init+json');
        //$p=with('api','payment')->post($data)->header($header)->call();

//        $response=with('api','back_card')->get('c627cb88-1695-4917-998b-9576db038c59')->call('body');

       // $header=['Content-Type'=>'application/vnd.kaiyuan.platform.bankaccount+json'];
       // $response=with('api','back_card')->header($header)->delete('c627cb88-1695-4917-998b-9576db038c59','1438846904166')->call('debug');


       // out($response);

        echo 5/0;
        exit;
	
		$header=array('Content-Type'=>'application/vnd.kaiyuan.platform.message+json');
		$response=with('api','message')->header($header)->put('d05796f2-205e-41ea-919f-83be5ae6741a','1439356861838',array('read'=>true))->call('debug');


        out($response);

	
	}



}