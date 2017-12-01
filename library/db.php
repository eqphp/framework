<?php

class db{

    static $pdo = array();

    static $pattern = array(
        'post' => 'insert into %s %s values %s',
        'delete' => 'delete from %s where %s',
        'patch' => 'update %s set %s where %s',
        'field' => 'select %s from %s where %s limit 0,1',
        'dsn' => 'mysql:host=%s;dbname=%s;port=%s;charset=%s;',
        );

    //执行SQL语句
    static function query($sql, $is_read = true){
        $flag = 0;
        $pdo = self::connect_db($flag);
        $result = $pdo->query($sql);
        if ($result) {
            logger::mysql($sql, $is_read);
            if (!$is_read && strpos($sql, 'insert') === 0) {
                return $pdo->lastInsertId();
            }
            return $result;
        }
        $message = $sql . implode(' : ', $pdo->errorInfo());
        logger::exception('mysql', $message);
        throw new Exception($message, 104);
    }

    //连接数据库
    static function connect_db($flag){
        if (isset(self::$pdo[$flag]) && self::$pdo[$flag] instanceof PDO) {
            return self::$pdo[$flag];
        }
        $db = (object)config('mysql.server.' . $flag);
        $dsn = sprintf(self::$pattern['dsn'], $db->host, $db->database, $db->port, $db->charset);
        $dsn = trim(str_replace(array('port=;', 'charset=;'), '', $dsn), ';');
        try {
            self::$pdo[$flag] = new PDO($dsn, $db->user, $db->password);//, array(PDO::ATTR_PERSISTENT => true)
            self::$pdo[$flag]->exec('set names ' . $db->charset);
            return self::$pdo[$flag];
        } catch (PDOException $e) {
            logger::Exception('mysql', $db->host . ' connect fail, ' . $e->getMessage());
            throw new Exception("mysql can't connect", 102);
        }
    }


    //添加数据记录
    static function post($table, $data, $option = ''){
        if (is_array($data) && $data) {
            $buffer = array_values($data);
            if (isset($buffer[0]) && is_array($buffer[0])) {
                $value = array();
                foreach ($buffer as $item) {
                    array_push($value, "('" . implode("','", array_values($item)) . "')");
                }
                $field = '(' . implode(',', array_keys($buffer[0])) . ')';
                return self::post($table, $field, $value);
            }
            $value = "('" . implode("','", array_values($data)) . "')";
            $data = '(' . implode(",", array_keys($data)) . ')';
        } else {
            $value = is_array($option) ? implode(',', $option) : $option;
        }

        $sql = sprintf(self::$pattern['post'], $table, $data, $value);
        return self::query($sql, false);
    }

    //删除数据记录
    static function delete($table, $condition){
        $condition = query::condition($condition);
        $sql = sprintf(self::$pattern['delete'], $table, $condition);
        return self::query($sql, false)->rowCount();
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
        return self::query($sql, false)->rowCount();
    }

    //查询-修改-创建
    static function put($table, $data, $condition){
        if (self::field($table, 'count(1)', $condition)) {
            return self::patch($table, $data, $condition);
        }
        return self::post($table, $data);
    }

    //返回单字段信息（表中单元格）
    static function field($table, $field, $condition = null){
        $pattern = self::$pattern['field'];
        if ($condition) {
            $condition = query::condition($condition);
            $sql = sprintf($pattern, $field, $table, $condition);
        } else {
            $pattern = str_replace('where %s ', '', $pattern);
            $sql = sprintf($pattern, $field, $table);
        }
        return self::query($sql, true)->fetch(PDO::FETCH_COLUMN);
    }

    //查询一条数据记录（数字、关联数组）
    static function record($sql, $mode = false){
        $mode = $mode ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
        return self::query($sql . ' limit 0,1', true)->fetch($mode);
    }

    //查询多条数据记录（数组-数组）模式
    static function batch($sql, $mode = false){
        $mode = $mode ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
        return self::query($sql, true)->fetchAll($mode);
    }

    //分页查询方法
    static function page($sql, $record_count, $page, $page_size = 20, $mode = false){
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
        logger::mysql($command, true);
        if ($command === 'begin') {
            $command = 'beginTransaction';
        } elseif ($command === 'rollback') {
            $command = 'rollBack';
        }

        call_user_func(array(self::connect_db(0), $command));
    }

}