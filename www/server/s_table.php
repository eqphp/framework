<?php

class s_table{

//定义表前缀
const table_prefix='ld_';

//定义全站数据表
static $table_list=array(
//幸运摩天轮
'w_config'=>'prize_config',
'w_log'=>'prize_log',
'w_setting'=>'prize_setting',
'w_address'=>'receive_address',
'w_member'=>'member_prize',
);

//加前缀
static function table($name){
// self::check_keyword(); //开启检测

if (in_array($name,array_keys(self::$table_list))) {
return self::table_prefix.self::$table_list[$name];
}

exit('Undefined table reflection: '.$name);
}




//检测表名、字段名是否含有mysql关键字
private static function check_keyword(){
$mysql_keyword_str=file_get_contents(dc_data_static.'txt/mysql_keyword.txt');
$mysql_keyword=explode(',',strtolower($mysql_keyword_str));
$table_list=array_values(self::$table_list); //db::get_field();

foreach ($table_list as $table) {

$table_name=strtolower(self::table_prefix.$table);

if (in_array($table_name,$mysql_keyword)) {
echo $table_name.' is a mysql keyword<hr>';
} else {
$tab_field_arr=db::get_field($table_name);

foreach ($tab_field_arr as $field) {
if (in_array(strtolower($field),$mysql_keyword)) {
echo strtolower($field).' ['.$table_name.'] is a mysql keyword<br>';
}
}

}
}

}


}