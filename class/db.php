<?php
//rely on: query logger
class db{

    private static $object=null;
    protected static $conn=null;

    //构造函数-初始化db
    private function __construct(){
        $config=config(null,'db');
        $server=$config['server'];
        $is_record_log=$config['log']['is_record_exception'];

        self::$conn=mysql_connect($server['host'].':'.$server['port'],$server['user'],$server['password']);
        if (!self::$conn) {
            $is_record_log and logger::exception('mysql',$server['host'].' connect failed');
            throw new Exception("mysql can't connect",102);
        }

        if (!mysql_select_db($server['database'],self::$conn)) {
            $info=$server['database'].' database select fail';
            $is_record_log and logger::exception('mysql',$info);
            throw new Exception($info,103);
        }

        mysql_query('set names '.$server['names'],self::$conn);
    }

    //禁止克隆
    final public function __clone(){
    }

    //返回一个数据库操作的唯一实例对象
    private static function get_instance(){
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


    //执行SQL语句
    static function query($sql,$is_read=true){
        self::get_instance();
        $result=mysql_query($sql,self::$conn);
        $log=config('log','db');
        $log['is_record_sql'] and logger::sql($sql,$is_read);
        if ($result) return $result;

        $info=$sql.' {'.mysql_error().'}';
        $log['is_record_exception'] and logger::exception('mysql',$info);
        throw new Exception($info,104);
    }

    //记录的添加、删除、修改、查询、事务
    //添加数据记录
    static function post($table,$data,$option=''){
        //以数组形式写入
        if (is_array($data)) {
            $field='('.implode(",",array_keys($data)).')';
            $value="('".implode("','",array_values($data))."')";
            self::query(sprintf('insert into %s %s values %s',$table,$field,$value),false);

            return mysql_insert_id();
        }

        //批量写入
        $value=is_array($option) ? implode(',',$option) : $option;
        self::query(sprintf('insert into %s %s values %s',$table,$data,$value),false);
        return mysql_insert_id();
    }

    //删除数据记录
    static function delete($table,$condition){
        $condition=query::condition($condition);
        return self::query(sprintf('delete from %s where %s',$table,$condition),false);

    }

    //修改数据记录
    static function patch($table,$data,$condition){
        if (is_string($data)) {
            $data=trim($data);
        } else {
            $field_info='';
            $data=(array)$data;
            foreach ($data as $key=>$value) {
                $field_info.=$key."='".$value."',";
            }
            $data=trim($field_info,',');
        }
        $condition=query::condition($condition);
        self::query(sprintf('update %s set %s where %s',$table,$data,$condition),false);
        return mysql_affected_rows();
    }

    //查询-修改-创建
    static function put($table,$data,$condition){
        if (self::field($table,'count(1)',$condition)) {
            return self::patch($table,$data,$condition);
        }
        return self::post($table,$data);
    }

    //返回单字段信息（表中单元格）
    static function field($table,$field,$condition=null){
        $execute_sql=sprintf('select %s from %s limit 0,1',$field,$table);
        if ($condition) {
            $condition=query::condition($condition);
            $execute_sql=sprintf('select %s from %s where %s limit 0,1',$field,$table,$condition);
        }
        $result=self::query($execute_sql);
        $record=mysql_fetch_array($result);
        if ($record && preg_match('/^[-\+]?\d+(\.\d+)?$/',$record[$field])) {
            return $record[$field]+0;
        }
        return isset($record[$field]) ? $record[$field] : null;
    }

    //查询一条数据记录（数字、关联数组）
    static function record($sql,$mode=false){
        $result=self::query($sql.' limit 0,1');
        return $mode ? mysql_fetch_object($result) : mysql_fetch_array($result,MYSQL_ASSOC);
    }

    //查询多条数据记录（数组-数组）模式
    static function batch($sql,$mode=false){
        $result=self::query($sql);
        $record=array();
        if ($mode) {
            while ($row=mysql_fetch_object($result)) {
                $record[]=$row;
            }
        } else {
            while ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
                $record[]=$row;
            }
        }
        mysql_free_result($result);
        return $record;
    }

    //分页查询方法
    static function page($sql,$record_count,$page,$page_size,$mode=false){
        empty($page_size) and $page_size=config('param.page_size','db');
        $page_count=ceil($record_count/$page_size);
        $page=max(1,$page);
        $page=($page > $page_count) ? $page_count : $page;
        $offset=($page_size > $record_count) ? 0 : (($page-1)*$page_size);
        $record=self::batch($sql." limit $offset,$page_size",$mode);
        return array($record_count,$page_count,$record);
    }

    //事务处理
    static function transaction($command){
        return self::query($command,false);
    }


}