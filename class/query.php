<?php

class query{

    public $sql='';
    public $option=array();
    static $keyword=array('select','from','join','left join','right join',
        'inner join','outer join','where','group','having','order','limit');


    //初始化查询参数
    function __construct($table,$prefix=''){
        $this->option['from']=$prefix.$table;
    }


    //构造参数
    function __call($method,$param){
        if (in_array($method,self::$keyword)) {
            $this->option[$method]=$param[0];
            return $this;
        }
    }

    //联表操作(仅支持同一联合类型)
    function join($join,$mode=''){
        $data=array('left','right','inner','outer');
        $key=($mode && in_array($mode,$data)) ? $mode.' join' : 'join';
        if (is_array($join)) {
            $buffer=array();
            foreach ($join as $table=>$condition) {
                $buffer[]=$key.' '.$table.' on '.$condition;
            }
            $this->option[$key]=substr(implode(' ',$buffer),strlen($key));
        }
        return $this;
    }


    //输出查询结果
    function out($mode='sql',$record_count=0,$page=1,$page_size=20){
        $this->sql='';
        if (!isset($this->option['select'])) {
            $this->option['select']='*';
        }
        foreach (self::$keyword as $key) {
            $value=($key == 'group' || $key == 'order') ? $key.' by' : $key;

            if ($key === 'where' && isset($this->option['where'])) {
                if (is_array($this->option['where'])) {
                    $this->option['where']=self::condition($this->option['where']);
                } elseif (preg_match('/^[0-9]*[1-9][0-9]*$/',$this->option['where'])) {
                    $this->option['where']='id='.$this->option['where'];
                }
            }

            if (isset($this->option[$key]) && trim($this->option[$key])) {
                $this->sql.=' '.$value.' '.trim($this->option[$key]);
            }

            unset($this->option[$key]);
        }
        $this->sql=trim($this->sql);

        switch($mode){
        case 'record':
            return db::record($this->sql);
        case 'batch':
            return db::batch($this->sql);
        case 'page':
            return db::page($this->sql,$record_count,$page,$page_size);
        default:
            return $this->sql;
        }
    }


    //构造sql查询条件
    static function condition($data){
        //处理逻辑连接符
        $logic=' and ';
        if (isset($data['logic'])) {
            $logic=' '.$data['logic'].' ';
            unset($data['logic']);
        }

        //处理字符串(本生sql)
        $condition=array();
        foreach (array('query','native','processed') as $name) {
            if (isset($data[$name])) {
                $condition[]='('.$data[$name].')';
                unset($data[$name]);
            }
        }

        //处理条件数据
        foreach ($data as $key=>$value) {
            $condition[]='('.self::parse_expression($key,$value).')';
        }

        return implode($logic,$condition);
    }


    //解析表达式
    private static function parse_expression($key,$value){
        if (is_numeric($value) && $value <= 2147483647) return $key.'='.$value;
        if (is_string($value) || is_numeric($value)) return $key.'="'.$value.'"';

        if (is_array($value)) {
            //基本条件查询
            if (preg_match('/^(eq|neq|gt|egt|lt|elt)$/i',$value[0])) {
                is_string($value[1]) and $value[1]='"'.$value[1].'"';
                $operator=array('eq'=>'=','neq'=>'<>','gt'=>'>',
                    'egt'=>'>=','lt'=>'<','elt'=>'<=',);
                return $key.$operator[$value[0]].$value[1];
            }

            //in范围查找
            if (in_array($value[0],array('in','not in'))) {
                if (is_array($value[1])) {
                    if (is_numeric($value[1][0])) {
                        $value[1]=implode(',',$value[1]);
                    } else {

                        $value[1]='"'.implode('","',$value[1]).'"';
                    }
                }
                return $key.' '.$value[0].'('.$value[1].')';
            }

            //between区间查找
            if (in_array($value[0],array('between','not between'))) {
                $param=is_string($value[1]) ? explode(',',$value[1]) : $value[1];
                $range=' '.$param[0].' and '.$param[1];
                is_string($param[0]) and $range=' "'.$param[0].'" and "'.$param[1].'"';
                return $key.' '.$value[0].$range;
            }

            //like模糊匹配
            if (in_array($value[0],array('like','not like'))) {
                if (is_array($value[1])) {
                    $buffer=array();
                    foreach ($value[1] as $param) {
                        $buffer[]=$key.' '.$value[0].' "'.$param.'"';
                    }
                    $logic=isset($value[2]) ? ' '.$value[2].' ' : ' or ';
                    return implode($logic,$buffer);
                }

                if (strpos($key,'|') !== false) {
                    $buffer=array();
                    foreach (explode('|',$key) as $field) {
                        $buffer[]='('.$field.' '.$value[0].' "'.$value[1].'")';
                    }
                    return implode(' or ',$buffer);
                }

                if (strpos($key,'&') !== false) {
                    $buffer=array();
                    foreach (explode('&',$key) as $field) {
                        $buffer[]='('.$field.' '.$value[0].' "'.$value[1].'")';
                    }
                    return implode(' and ',$buffer);
                }
                return $key.' '.$value[0].' "'.$value[1].'"';
            }

            //数学区间查询(1,9)/[2,3)
            if ($value[0] === 'extent') {
                $logic=isset($value[2]) ? ' '.$value[2].' ' : ' && ';
                $operator=array('('=>'>','['=>'>=',')'=>'<',']'=>'<=');
                preg_match('/^(\(|\[)(.*),(.*)(\)|\])$/',$value[1],$param);
                $result='';
                isset($param[2]) && $result.=$key.$operator[$param[1]].$param[2];
                isset($param[4]) && $result.=$logic.$key.$operator[$param[4]].$param[3];
                return $result;
            }
        }
    }

    //构造sql查询语句
    static function sql($option,$from='',$where=null){
        if (is_array($option)) {
            $sql='';
            $key_arr=array('select','from','where','group','having','order','limit');
            foreach ($key_arr as $key) {
                $value=($key == 'group' || $key == 'order') ? $key.' by' : $key;
                $sql.=(isset($option[$key]) && trim($option[$key])) ? ' '.$value.' '.trim($option[$key]) : '';
            }
            return $sql;
        }

        $sql='select '.$option.' from '.$from;
        if ($where !== null) {
            $sql.=' where '.(preg_match('/^[0-9]*[1-9][0-9]*$/',$where) ? 'id='.$where : $where);
        }
        return $sql;
    }

    //资源回收
    function __destruct(){
        unset($this->option,$this->sql);
    }

}