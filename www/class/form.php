<?php
class form{

    //创建表单
    static function add($type,$name='',$value='',$attr='',$way=''){
        $attr_arr=$attr ? explode('|',$attr) : array($name,$name);
        $way_arr=$way ? explode('|',$way) : array('','');
        $from=null;

        if ($type == 'select') {
            if (!$value) return $from;
            $from='<select id="'.$attr_arr[0].'" class="'.$attr_arr[1].'" name="'.$name.'">';

            foreach ($value as $fk=>$fv) {
                $from.='<option value="'.$fk.'"';
                if ($way != '' && $fk == $way) {
                    $from.=' selected';
                }
                $from.='>'.$fv."</option>";
            }

            $from.="</select>";
            return $from;
        }

        if ($type == 'checkbox') {
            if (!is_array($value)) return $from;
            foreach ($value as $fk=>$fv) {
                $from.='<input id="'.$attr_arr[0].'_'.$fk.'" class="'.$attr_arr[1].'" name="'.$name.'[]" type="checkbox" value="'.$fk.'"';
                if (in_array($fk,$way_arr)) {
                    $from.=' checked';
                }
                $from.=' />'.$fv;
            }
            return $from;
        }

        if ($type == 'radio') {
            if (!is_array($value)) return $from;
            foreach ($value as $fk=>$fv) {
                $from.='<input id="'.$attr_arr[0].'_'.$fk.'" class="'.$attr_arr[1].'" name="'.$name.'" type="radio" value="'.$fk.'"';
                if ($way != '' && $fk == $way) {
                    $from.=' checked';
                }
                $from.=' />'.$fv;
            }
            return $from;
        }

        if (in_array($type,array('text','password'))) {
            $from='<input id="'.$attr_arr[0].'" class="'.$attr_arr[1].'" name="'.$name.'" type="'.$type.'" value="'.$value.'" size="'.$way_arr[0].'" maxlength="'.$way_arr[1].'"';
            if ($way_arr[2]) {
                $from.=' readonly';
            }
            $from.=' />';
            return $from;
        }

        if (in_array($type,array('hidden','file'))) {
            return '<input id="'.$attr_arr[0].'" class="'.$attr_arr[1].'" name="'.$name.'" type="'.$type.'" value="'.$value.'" />';
        }

        if ($type == 'img') {
            $server_host=isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
            return '<img id="'.$attr_arr[0].'" class="'.$attr_arr[1].'" src="http://'.$server_host.'/code/img/?check='.$name.'&r_num=" alt="看不清？点击更换" title="看不清？点击更换" onclick="this.src=this.src+Math.random();">';
        }

        if ($type == 'textarea') {
            return '<textarea id="'.$attr_arr[0].'" class="'.$attr_arr[1].'" name="'.$name.'">'.$value.'</textarea>';
        }

        if (in_array($type,array('submit','button','reset'))) {
            return '<input id="'.$attr_arr[0].'" class="'.$attr_arr[1].'" name="'.$name.'" type="'.$type.'" value="'.$value.'" />';
        }

        if ($type == 'form') {
            return '<form id="'.$attr_arr[0].'" class="'.$attr_arr[1].'" name="'.$name.'" action="'.$value.'" method="'.$way_arr[0].'" target="'.$way_arr[1].'">';
        }

        if ($type == 'end') {
            return "</form>";
        }

        http::js('create form fail !','alert');
    }

    //接收处理get传值
    static function get($name,$mode='int'){

        //REQUEST传值、未过滤，慎用
        if ($mode == 'request') {
            if (!isset($_REQUEST[$name])) return false;
            return get_magic_quotes_gpc() ? $_REQUEST[$name] : addslashes($_REQUEST[$name]);
        }

        if ($mode == 'isset') return isset($_GET[$name]); //是否初始化
        if (!isset($_GET[$name])) return false;

        $target_data=get_magic_quotes_gpc() ? $_GET[$name] : addslashes($_GET[$name]);

        //ID,自然数、GET的整型(0-N,ID、number)
        if ($mode == 'int') return abs((int)($target_data));

        //命名、名字、变量名(字母、下划线、数字，字母开头，转小写)
        if ($mode == 'name') return trim(htmlspecialchars(strip_tags(strtolower($target_data))));

        //数字、负数(正负数)
        if ($mode == 'number') return $target_data+0;

        //将get值原味输出
        if ($mode == 'get') return $target_data;
    }


    //接收并处理表单所传递的值
    static function post($name,$mode='title'){

        //输出可用参数类型
        if ($mode == 'tip') return 'title/int/info/text/number/float/account/isset/arr_str/post';

        if ($mode == 'isset') return isset($_POST[$name]); //是否初始化
        if (!isset($_POST[$name])) return false;

        $target_data=get_magic_quotes_gpc() ? $_POST[$name] : addslashes($_POST[$name]);

        //标题、关键词(去空、特殊字符、html标签)
        if ($mode == 'title') return trim(htmlspecialchars(strip_tags($target_data)));

        //ID,自然数、POST的整型(0-N,ID、number)
        if ($mode == 'int') return abs((int)($target_data));

        //摘要、描述(去除html标签)
        if ($mode == 'info') return trim(strip_tags($target_data));

        //介绍、详细内容(就留允许的html标签)
        if ($mode == 'text') {
            $allow_tags='<ul><ol><li><p><h1><h2><h3><h4><h5><h6><table><tr><th><td>';
            $allow_tags.='<img><a><span><b><i><em><cite><strong><br><hr>';
            return trim(strip_tags($target_data,$allow_tags));
        }

        //负数(正负数)
        if ($mode == 'number') return $target_data+0;

        //小数、浮点数(货币、概率)
        if ($mode == 'float') return (float)($target_data);

        //邮箱、用户名(注册账号时不区分大小写)
        if ($mode == 'account') return trim(htmlspecialchars(strip_tags(strtolower($target_data))));

        //联合复选框(checkbox)
        if ($mode == 'arr_str') return implode('|',$target_data);

        //将post原味输出
        if ($mode == 'post') return $target_data;
    }

    //正则过滤
    static function regular($name,$regexp,$mode='post'){
        $target_data=$name;

        if ($mode === 'post') {
            if (!isset($_POST[$name])) return null;
            $target_data=get_magic_quotes_gpc() ? $_POST[$name] : addslashes($_POST[$name]);
        }

        if ($mode === 'get') {
            if (!isset($_GET[$name])) return null;
            $target_data=get_magic_quotes_gpc() ? $_GET[$name] : addslashes($_GET[$name]);
        }

        if (safe::reg($target_data,$regexp)) {
            return $target_data;
        }
    }


    //批量安全过滤
    static function filter($data){
        $buffer=null;
        $rule_arr=explode('/',self::post(null,'tip'));
        if (is_array($data)) {
            foreach ($data as $option=>$rule) {

                if (!in_array($rule,$rule_arr)) { //过滤若未定义
                    http::js('undefined '.$rule,'alert');
                }

                $buffer[$option]=self::post($option,$rule);
            }
        }
        return $buffer;
    }


    //单项数据验证
    static function check($value,$rule,$type='regex'){

        $type=strtolower(trim($type));
        switch($type){

        case 'in': //是否在指定范围值之内，逗号分隔字符串或者数组
        case 'notin':
            $range=is_array($rule) ? $rule : explode(',',$rule);
            return ($type == 'in') ? in_array($value,$range) : !in_array($value,$range);

        case 'between': //在某个区间内
        case 'notbetween': //在某个区间外
            list($min,$max)=is_array($rule) ? $rule : explode(',',$rule);
            return ($type == 'between') ? ($value >= $min && $value <= $max) : ($value < $min || $value > $max);

        case 'equal': //是否相等
        case 'notequal': //是否不等
            return ($type == 'equal') ? ($value == $rule) : ($value != $rule);

        case 'length': //长度
            $length=mb_strlen($value,'utf-8');
            if (strpos($rule,',')) { //指定长度区间内
                list($min,$max)=explode(',',$rule);
                return $length >= $min && $length <= $max;
            } else { //长度相等
                return $length == $rule;
            }

        case 'expire': //有效期
            $now_time=time();
            list($start,$end)=explode(',',$rule);
            $start=is_numeric($start) ? $start : strtotime($start);
            $start=is_numeric($end) ? $end : strtotime($end);
            return $now_time >= $start && $now_time <= $end;

        case 'regex':
        default: //默认使用正则验证 可以使用验证类中定义的验证名称
            //检查附加规则
            return safe::reg($value,$rule);
        }
    }

    //单项数据验证并提示错误信息
    static function check_tip($value,$rule,$type,$tip){
        if (self::check($value,$rule,$type)) return true;
        if (is_array($tip)) http::json($tip);
        http::script($tip,'alert');
    }


    //查询form类方法
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、创建表单：add($type,$name,$value="",$attr="",$way="")<br>';
        $info.='2、GET传值：get($name,$mode="int")<br>';
        $info.='3、POST传值：post($name,$mode="title")<br>';
        $info.='4、批量过滤：filter($data)<br>';
        // $info.='5、批量验证：valid($data,$option,$tip)<br>';
        // $info.='6、扩展验证：verify($value,$option)<br>';
        // $info.='7、验证提示：verify_tip($value,$option,$tip)<br>';
        $info.='8、基本验证：check($value,$rule,$type="regex")<br>';
        $info.='9、验证提示：check_tip($value,$rule,$type,$tip)</font><br>';
        return $info;
    }

}