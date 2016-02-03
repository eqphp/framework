<?php
//rely on: query logger
class mysql{

    private static $object=array();
    protected static $conn=array();
    private $i;

    //构造函数-初始化db
    protected function __construct($i=0){
        $this->i=$i;
        $config=config(null,'mysql');
        $server=$config['server'][$this->i];
        $is_record_log=$config['log']['is_record_exception'];

        self::$conn[$this->i]=mysql_connect($server['host'],$server['user'],$server['password'],true);

        if (!self::$conn[$this->i]) {
            $info='database server '.$this->i.' connect fail';
            $is_record_log and logger::exception('mysql',$info);
            throw new Exception($info,102);
        }

        if (!mysql_select_db($server['database'],self::$conn[$this->i])) {
            $info='database '.$this->i.' select fail';
            $is_record_log and logger::exception('mysql',$info);
            throw new Exception($info,103);
        }

        mysql_query('set names '.$server['names'],self::$conn[$this->i]);
    }

    //禁止克隆
    final public function __clone(){
    }

    //返回一个数据库操作的唯一实例对象
    static function get_instance($i){
        if (!(isset(self::$object[$i]) && self::$object[$i] instanceof self)) {
            self::$object[$i]=new self($i);
        }
        return isset(self::$object[$i]) ? self::$object[$i] : null;
    }

    //执行SQL语句
    function query($sql,$is_read=true){
        $result=mysql_query($sql,self::$conn[$this->i]);
        $log=config('log','mysql');
        $log['is_record_sql'] and logger::sql($sql,$is_read);
        if ($result) return $result;

        $info=$sql.' {'.mysql_error().'}';
        $log['is_record_exception'] and logger::exception('mysql',$info);
        throw new Exception($info,104);
    }

    //记录的添加、删除、修改、查询、事务
    //添加数据记录
    function post($table,$data,$option=''){
        //以数组形式写入
        if (is_array($data)) {
            $field='('.implode(",",array_keys($data)).')';
            $value="('".implode("','",array_values($data))."')";
            $this->query(sprintf('insert into %s %s values %s',$table,$field,$value),false);
            return mysql_insert_id();
        }

        //批量写入
        $value=is_array($option) ? implode(',',$option) : $option;
        $this->query(sprintf('insert into %s %s values %s',$table,$data,$value),false);
        return mysql_insert_id();
    }

    //删除数据记录
    function delete($table,$condition){
        $condition=query::condition($condition);
        return $this->query(sprintf('delete from %s where %s',$table,$condition),false);
    }

    //修改数据记录
    function patch($table,$data,$condition){
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
        $this->query(sprintf('update %s set %s where %s',$table,$data,$condition),false);
        return mysql_affected_rows();
    }

    //查询-修改-创建
    function put($table,$data,$condition){
        if (self::field($table,'count(1)',$condition)) {
            return self::patch($table,$data,$condition);
        }
        return self::post($table,$data);
    }
    
    //返回单字段信息（表中单元格）
    function field($table,$field,$condition=null){
        $execute_sql=sprintf('select %s from %s limit 0,1',$field,$table);
        if ($condition) {
            $condition=query::condition($condition);
            $execute_sql=sprintf('select %s from %s where %s limit 0,1',$field,$table,$condition);
        }

        $result=$this->query($execute_sql);
        $record=mysql_fetch_array($result);
        if ($record && preg_match('/^[-\+]?\d+(\.\d+)?$/',$record[$field])) {
            return $record[$field]+0;
        }
        return isset($record[$field]) ? $record[$field] : null;
    }

    //查询一条数据记录（数字、关联数组）
    function record($sql,$mode=false){
        $result=$this->query($sql.' limit 0,1');
        return $mode ? mysql_fetch_object($result) : mysql_fetch_array($result,MYSQL_ASSOC);
    }

    //查询多条数据记录（数组-数组）模式
    function batch($sql,$mode=false){
        $result=$this->query($sql);
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
    function page($sql,$record_count,$page,$page_size,$mode=false){
        empty($page_size) and $page_size=config('db_0.page_size','mysql');
        $page_count=ceil($record_count/$page_size);
        $page=max(1,$page);
        $page=($page > $page_count) ? $page_count : $page;
        $offset=($page_size > $record_count) ? 0 : (($page-1)*$page_size);
        $record=$this->batch($sql." limit $offset,$page_size",$mode);
        return array($record_count,$page_count,$record);
    }

    //事务处理
    function transaction($command){
        return $this->query($command,false);
    }


}
