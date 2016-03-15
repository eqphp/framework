<?php

//rely on: query logger
class db{

    static $object = null;
    private static $conn = null;

    static $pattern = array(
        'post' => 'insert into %s %s values %s',
        'delete' => 'delete from %s where %s',
        'patch' => 'update %s set %s where %s',
        'field' => 'select %s from %s where %s limit 0,1',
    );

    //构造函数-初始化db
    private function __construct(){
        $db = (object)config('server', 'db');
        self::$conn = mysqli_connect($db->host . ':' . $db->port, $db->user, $db->password);

        if (self::$conn) {
            if (mysqli_select_db(self::$conn, $db->database)) {
                mysqli_query(self::$conn, 'set names ' . $db->charset);
            } else {
                $message = 'database select fail';
                if (config('log.is_record_exception', 'db')) {
                    logger::exception('mysql', $message);
                }
                throw new Exception($message, 103);
            }
        } else {
            $message = 'mysqli connect fail';
            if (config('log.is_record_exception', 'db')) {
                logger::exception('mysql', $message);
            }
            throw new Exception($message, 102);
        }

    }

    //禁止克隆
    final public function __clone(){
    }

    //返回唯一实例
    private static function get_instance(){
        if (!self::$object instanceof self) {
            self::$object = new self();
        }
        return self::$object;
    }

    //析构函数-资源回收
    function __destruct(){
        if (is_resource(self::$conn)) {
            mysqli_close(self::$conn);
        }
        self::$conn = self::$object = null;
    }


    //执行SQL语句
    static function query($sql, $is_read = true){
        self::get_instance();
        $result = mysqli_query(self::$conn, $sql);
        if ($result) {
            if (config('log.is_record_sql', 'db')) {
                logger::sql($sql, $is_read);
            }
            return $result;
        }

        $message = $sql . ' {' . mysqli_error(self::$conn) . '}';
        if (config('log.is_record_exception', 'db')) {
            logger::exception('mysql', $message);
        }
        throw new Exception($message, 104);
    }

    //添加数据记录
    static function post($table, $data, $option = ''){
        if (is_array($data)) {
            $value = "('" . implode("','", array_values($data)) . "')";
            $data = '(' . implode(",", array_keys($data)) . ')';
        } else {
            $value = is_array($option) ? implode(',', $option) : $option;
        }
        $sql = sprintf(self::$pattern['post'], $table, $data, $value);
        self::query($sql, false);
        return mysqli_insert_id(self::$conn);
    }

    //删除数据记录
    static function delete($table, $condition){
        $condition = query::condition($condition);
        $sql = sprintf(self::$pattern['delete'], $table, $condition);
        self::query($sql, false);
        return mysqli_affected_rows(self::$conn);
    }

    //修改数据记录
    static function patch($table, $data, $condition){
        if (is_string($data)) {
            $data = trim($data);
        } else {
            list($field_info, $data) = array('', (array)$data);
            foreach ($data as $key => $value) {
                $field_info .= $key . "='" . $value . "',";
            }
            $data = trim($field_info, ',');
        }
        $condition = query::condition($condition);
        $sql = sprintf(self::$pattern['patch'], $table, $data, $condition);
        self::query($sql, false);
        return mysqli_affected_rows(self::$conn);
    }

    //查询-修改-创建
    static function put($table, $data, $condition){
        if (self::field($table, 'count(1)', $condition)) {
            return self::patch($table, $data, $condition);
        }
        return self::post($table, $data);
    }

    //返回单字段信息（表中单元格）
    static function field($table, $field, $condition = null, $is_numeric = false){
        $pattern = self::$pattern['field'];
        if ($condition) {
            $condition = query::condition($condition);
            $sql = sprintf($pattern, $field, $table, $condition);
        } else {
            $pattern = str_replace('where %s ', '', $pattern);
            $sql = sprintf($pattern, $field, $table);
        }
        $record = mysqli_fetch_array(self::query($sql));
        if ($record && isset($record[$field])) {
            return $is_numeric ? $record[$field] + 0 : $record[$field];
        }
    }

    //查询一条数据记录（数字、关联数组）
    static function record($sql, $mode = false){
        $result = self::query($sql . ' limit 0,1');
        return $mode ? mysqli_fetch_object($result) : mysqli_fetch_array($result, MYSQLI_ASSOC);
    }

    //查询多条数据记录（数组-数组）模式
    static function batch($sql, $mode = false){
        $result = self::query($sql);
        $record = array();
        if ($mode) {
            while ($row = mysqli_fetch_object($result)) {
                $record[] = $row;
            }
        } else {
            while ($row = mysqli_fetch_array($result)) {
                $record[] = $row;
            }
        }
        mysqli_free_result($result);
        return $record;
    }

    //分页查询方法
    static function page($sql, $record_count, $page, $page_size = null, $mode = false){
        if (is_null($page_size)) {
            $page_size = config('query.page_size', 'db');
        }
        list($page, $offset) = array(max(1, $page), 0);
        $page_count = ceil($record_count / $page_size);
        if ($page > $page_count) {
            $page = $page_count;
        }
        if ($page_size <= $record_count) {
            $offset = $page_size * ($page - 1);
        }
        $record = self::batch("{$sql} limit {$offset},{$page_size}", $mode);
        return array($record_count, $page_count, $record);
    }

    //事务处理
    static function transaction($command){
        return self::query($command, false);
    }


}