<?php

class a_manage extends a_access{

function index(){
//验证权限，跳转提示页面
if (!in_array(parent::visite_access,$this->admin_access)) {
http::skip('message/login/forbid');
}

//接收请求参数
$category=rq(2); //类型(1,已读/0,未读/全部)
$page=rq(3,1); //页码

//获取删除和回复权限
$del_access=in_array(parent::del_access,$this->admin_access);
$reply_access=in_array(parent::reply_access,$this->admin_access);

//调用查询模型
$message=self::message_model($category,$page,10);
//处理分页导航
$page_url=dc_url.'manage/'.$category.'/';
$page_nav=s_page::mark($page_url,$message['info'][0],$page,3);

//视图赋值
$tpl=smarty('admin');

$head['frame']='_self';
$head['title']='留言管理_EQPHP案例留言本';
$tpl->assign('head',$head);

$tpl->assign('del_access',$del_access);
$tpl->assign('reply_access',$reply_access);

$tpl->assign('rs_count',$message['num']);
$tpl->assign('message',$message['info'][1]);
$tpl->assign('page_nav',$page_nav);

//渲染视图模板
$tpl->display('message/manage');
}


//数据查询模型(可放model里)
private static function message_model($category,$page=1,$page_size=15){
//处理查询条件
$condition=null;
$category_list=array('all'=>null,'unread'=>0,'read'=>1);
if (isset($category_list[$category]) && $category_list[$category]!==null) {
$condition.='is_view='.$category_list[$category];
}

//构造SQL语句
$option['select']='id,is_view,pub_time,user_name,tel,phone,email,message';
$option['from']=parent::table;
$option['where']=$condition;
$option['order']='id desc';

//入库查询
$rs_count=db::field(parent::table,'count(1)',$condition);
$message=db::page_list(sql($option),$rs_count,$page,$page_size);

//返回结果
return array('num'=>$rs_count,'info'=>$message);
}

}