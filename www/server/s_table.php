<?php

class s_table{

    //定义表前缀
    const table_prefix='eqphp_';

    //定义全站数据表
    static $table_list=array(//用户
        'u_user'=>'user','u_info'=>'user_info','u_login'=>'user_login','u_register'=>'user_register','u_follow'=>'user_follow',);

    //加前缀
    static function table($name){
        // self::check_keyword(); //开启检测

        if (in_array($name,array_keys(self::$table_list))) {
            return self::table_prefix.self::$table_list[$name];
        }

        exit('undefined table reflection: '.$name);
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