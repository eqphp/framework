<?php

class a_access{

const visite_access=1; //设置本页面权限
const del_access=2; //设置本页面权限
const reply_access=3; //设置本页面权限


const table='message'; //设置数据表
protected $admin_account=null;
protected $admin_access=null;

		 
function __construct(){
$admin=session('admin');

if ($admin) {
$this->admin_account=$admin['account'];
$this->admin_access=explode(',',$admin['access']);
}

//若用户未登录，跳转至登录页面
if (!$this->admin_account) {
http::skip('message/login');
}
}

//清理资源
function __destruct(){
unset($this->admin_account,$this->admin_access);
}

}