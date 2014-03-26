<?php

class debug{

    //开发模式-调试方法
    static function out($dim='no $dim !',$mode=true,$is_exit=false){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n<pre>\n";
        if ($mode == 2) {
            var_export($dim);
        } else {
            $mode ? print_r($dim) : var_dump($dim);
        }
        $is_exit ? (print '</pre>') : (exit('</pre>'));
    }

    //获取系统信息
    static function info($type=1){
        $type_list=array('basic','const','variable','function','class','interface','file');
        if (is_int($type) && $type < 7) $type=$type_list[$type];
        switch($type){
        case 'const':
            $const_arr=get_defined_constants(true);
            return $const_arr['user'];
            //2因作用域，请在外边直接调用函数
        case 'variable':
            return 'please use: get_defined_vars()';
        case 'function':
            $fun_arr=get_defined_functions();
            return $fun_arr['user'];
        case 'class':
            return array_slice(get_declared_classes(),125);
        case 'interface':
            return array_slice(get_declared_interfaces(),10);
        case 'file':
            return get_included_files();
        default:
            return array('system'=>php_uname(),'service'=>php_sapi_name(),'php_version'=>PHP_VERSION,'frame_name'=>config('frame|name'),'frame_version'=>config('frame|version'),'magic_quotes'=>get_magic_quotes_gpc(),'time_zone'=>date_default_timezone_get());
        }
    }

    //取得微秒数、内存消耗
    static function set_flag(){
        list($usec,$sec)=explode(' ',microtime());
        $microtime=((float)$usec+(float)$sec);
        return array($microtime,memory_get_usage());
    }

    //计算运行时间,内存消耗
    static function used($begin,$end){
        $diff_time=round(($end[0]-$begin[0]),3);
        $diff_memory=round(($end[1]-$begin[1])/128,3);
        $max_memory=round((memory_get_peak_usage())/128,3);
        return array($diff_time.'s',$diff_memory.'kb',$max_memory.'kb');
    }

    //xhprof调试工具封装
    static function xhprof($xhprof_data,$res_name="xhprof_res"){
        include_once dc_root.'xhprof_lib/xhprof_lib.php';
        include_once dc_root.'xhprof_lib/xhprof_runs.php';
        $xhprof_runs=new XHProfRuns_Default();
        $run_id=$xhprof_runs->save_run($xhprof_data,$res_name);
        $show_url=dc_url.'xhprof_html/index.php?run='.$run_id.'&source='.$res_name;
        return '<a href="'.$show_url.'" target="_blank">XHPROF_RESULT</a>';
    }

    //查询try类方法
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、调试方法：out($dim="no $dim !",$mode=false,$stop=false)<br>';
        $info.='2、系统信息：info($type=1)<br>';
        $info.='3、性能旗帜：set_tm()<br>';
        $info.='4、获取性能：tm($start_tm,$stop_tm)<br>';
        $info.='5、xhprof：xhprof($xhprof_data,$res_name="xhprof_res")</font><br><br>';
        return $info;
    }


}