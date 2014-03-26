<?php

class query{

    public $sql='';
    public $option=array();
    static $keyword=array('select','from','join','left join','right join','inner join','outer join','where','group','having','order','limit');


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
            foreach ($join as $table=>$condition) {
                $buffer[]=$key.' '.$table.' on '.$condition;
            }
            $this->option[$key]=substr(implode(' ',$buffer),strlen($key));
        }

        return $this;
    }


    //输出查询结果
    function out($mode='sql',$rs_count=0,$now_page=1,$page_size=20){
        $this->sql='';
        foreach (self::$keyword as $key) {
            $value=($key == 'group' || $key == 'order') ? $key.' by' : $key;

            if ($key === 'where' && isset($this->option['where']) && is_array($this->option['where'])) {
                $this->option['where']=self::condition($this->option['where']);
            }

            if (isset($this->option[$key]) && trim($this->option[$key])) {
                $this->sql.=' '.$value.' '.trim($this->option[$key]);
            }

            unset($this->option[$key]);
        }
        $this->sql=trim($this->sql);

        switch($mode){
        case 'rs':
            return db::rs($this->sql);
        case 'list':
            return db::rs_list($this->sql);
        case 'page':
            return db::page_list($this->sql,$rs_count,$now_page,$page_size);
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
        if (isset($data['query'])) {
            $condition[]='('.$data['query'].')';
            unset($data['query']);
        }

        //处理条件数据
        foreach ($data as $key=>$value) {
            $condition[]='('.self::parse_expression($key,$value).')';
        }

        return implode($logic,$condition);
    }


    //解析表达式
    private static function parse_expression($key,$value){
        if (is_numeric($value)) return $key.'='.$value;
        if (is_string($value)) return $key.'="'.$value.'"';

        if (is_array($value)) {
            //基本条件查询
            if (preg_match('/^(eq|neq|gt|egt|lt|elt)$/i',$value[0])) {
                is_string($value[1]) && $value[1]='"'.$value[1].'"';
                $operator=array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=',);
                return $key.$operator[$value[0]].$value[1];
            }

            //in范围查找
            if (in_array($value[0],array('in','not in'))) {
                is_array($value[1]) && $value[1]=implode(',',$value[1]);
                return $key.' '.$value[0].'('.$value[1].')';
            }

            //between区间查找
            if (in_array($value[0],array('between','not between'))) {
                $param=is_string($value[1]) ? explode(',',$value[1]) : $value[1];
                return $key.' '.$value[0].' '.$param[0].' and '.$param[1];
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

            return '';
        }
    }


    //资源回收
    function __destruct(){
        unset($this->option,$this->sql);
    }


}