<?php
class db{

private static $object=null;
protected static $conn=null;

//构造函数-初始化db
protected function __construct(){
$db=config('server','db');
$exception=config('exception','log');
$debug=config('param|debug','db');

self::$conn=mysql_connect($db['host'].':'.$db['port'],$db['user'],$db['pwd']);
if (!self::$conn) {
$exception['mysql_connect'] && log::exception('mysql',$db['host'].' connect failed');
$debug && exit("mysql can't connect !");
}

if (!mysql_select_db($db['dbase'],self::$conn)) {
$exception['mysql_select'] && log::exception('mysql',$db['dbase'].' select failed');
$debug && exit($db['dbase'].' select failed');
}

mysql_query('set names '.$db['names'],self::$conn);
}

//禁止克隆
final public function __clone(){}

//返回一个数据库操作的唯一实例对象
private static function instance(){
if (!(self::$object instanceof self)) {
self::$object=new self();
}
return self::$object;
}

//析构函数-资源回收
function __destruct(){
if (is_resource(self::$conn)) {
mysql_close(self::$conn);
}
self::$conn=null;
self::$object=null;
}



/********************************
///////以下方法可对外访问////////
********************************/

//执行SQL语句
static function query($sql,$is_read=true){
self::instance();
$result=mysql_query($sql,self::$conn);
config('log|mysql_sql','log') && log::sql($sql,$is_read);
if ($result) return $result;

$info=$sql.' {'.mysql_error().'}';
config('exception|sql_error','log') && log::exception('mysql',$info);
config('param|debug','db') && exit($info);
}

//记录的添加、删除、修改、查询、事务
static function add($table,$data,$option=''){ //添加数据记录
//以数组形式写入
if (is_array($data)) {
$field='('.implode(",",array_keys($data)).')';
$value="('".implode("','",array_values($data))."')";
self::query("insert into $table $field values $value",false);
return mysql_insert_id();
}

//批量写入
$value=is_array($option)?implode(',',$option):$option;
self::query("insert into $table $data values $value",false);
return mysql_insert_id();
}

static function del($table,$condition){ //删除数据记录
$condition=preg_match('/^[0-9]*[1-9][0-9]*$/',$condition)?'id='.$condition:$condition;
return self::query("delete from $table where $condition",false);
}

static function mod($table,$data,$condition){ //修改数据记录
//以数组形式修改
if (is_array($data)) {
$feild_info='';
foreach ($data as $key=>$value) {
$feild_info.=$key."='".$value."',";
}
$data=trim($feild_info,',');
}

$condition=preg_match('/^[0-9]*[1-9][0-9]*$/',$condition)?'id='.$condition:$condition;
return self::query("update $table set $data where $condition",false);
}

static function field($table,$field,$condition=null){ //返回单字段信息（表中单元格）
if ($condition===null) {
$execute_sql="select $field from $table limit 0,1";
} else {
$condition=preg_match('/^[0-9]*[1-9][0-9]*$/',$condition)?'id='.$condition:$condition;
$execute_sql="select $field from $table where $condition limit 0,1";
}
$result=self::query($execute_sql);
$rs=mysql_fetch_array($result);
return $rs[$field];
}

static function rs($sql,$mode=false){ //查询一条数据记录（数字、关联数组）
$result=self::query($sql.' limit 0,1');
return $mode?mysql_fetch_object($result):mysql_fetch_array($result,MYSQL_ASSOC);
}

static function rs_list($sql,$mode=false){ //查询多条数据记录（数组-数组）模式
$result=self::query($sql);
$arr=null;
while($row=$mode?mysql_fetch_object($result):mysql_fetch_array($result,MYSQL_ASSOC)){
$arr[]=$row;
}
mysql_free_result($result);
return $arr;
}

static function page_list($sql,$rs_count,$page,$page_size,$mode=false){ //分页查询方法
if (!$page_size) $page_size=config('param|page_size','db');
$page_count=ceil($rs_count/$page_size);
$page=$page?$page:1;
$page=($page>$page_count)?$page_count:$page;
$start_rs=($page_size>$rs_count)?0:(($page-1)*$page_size);
$data=self::rs_list($sql." limit $start_rs,$page_size",$mode);
return array((int)$page_count,$data);
}

static function transaction($result){ //事务处理
if ($result) {
self::query("commit");
return true;
}
self::query("rollback");
}

//取得指定库/表字段
static function get_field($table_name=null){
if ($table_name) {
$result=self::query('select * from '.$table_name.' limit 0,1');
while ($field=mysql_fetch_field($result)) {
$name_list[]=$field->name;
}
} else {
$result=self::query("show tables");
while($row=mysql_fetch_array($result,MYSQL_NUM)){
$name_list[]=$row[0];
}
}
return $name_list;
}

//db类方法帮助提示
static function tip(){
$info='<br><font color="green">执行请求：query($sql,$is_read=true)<br>';
$info.='指定字段：field($det_name,$table,$condition)<br>';
$info.='一条记录：rs($sql,$mode=false)<br>';
$info.='多条记录：rs_list($sql,$mode=false)<br>';
$info.='记录分页：page_list($sql,$rs_count,$page,$page_size,$mode=false)<br>';
$info.='插入记录：add($table,$data,$option="")<br>';
$info.='修改记录：mod($table,$data,$condition)<br>';
$info.='删除记录：del($table,$condition)<br>';
$info.='事务处理：transaction($result)<br>';
$info.='查表字段：get_field($table_name=null)</font><br><br>';
return $info;
}


}