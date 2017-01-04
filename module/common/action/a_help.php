<?php

class a_help{

    static function index(){
        out(debug::info());
    }

    static function phpinfo(){
        phpinfo();
    }

    static function manual(){
        http::redirect('file/manual');
    }

    //函数比较
    static function compare(){
        header(UTF8);
        $data = array(null, array(), false, true, 1, 42, -1, 0, '', '0', '1', '-1', 'php', 'true', 'false');
        echo CSS . PHP_EOL . '<table class="m10 border compare">' . PHP_EOL;
        echo '<tr><td>&nbsp;</td><td>gettype()</td><td>is_null()</td><td>isset()</td><td>empty()</td><td>if()</td></tr>' . PHP_EOL;
        echo '<tr bgcolor="#efefef"><td>var/undefined</td><td>null</td><td>|</td><td>○</td><td>|</td><td>○</td></tr>' . PHP_EOL;
        foreach ($data as $key => $value) {
            $color = ($key % 2) ? '#efefef' : 'white';
            echo '<tr bgcolor="' . $color . '">';
            echo html::td(var_export($value, true));
            echo html::td(gettype($value));
            echo html::td(is_null($value) ? '|' : '○');
            echo html::td(isset($value) ? '|' : '○');
            echo html::td(empty($value) ? '|' : '○');
            echo html::td($value ? '|' : '○');
            echo '</tr>' . PHP_EOL;
        }
        echo '</table>';
    }

    //松散比较
    static function compare2(){
        header(UTF8);
        $data = array(null, array(), true, false, 1, -1, 0, '', '0', '1', '-1', 'php');
        echo CSS . PHP_EOL . '<table class="m10 border compare">' . PHP_EOL;
        echo '<tr bgcolor="white"><td>==</td><td>null</td><td>array()</td><td>true</td><td>false</td><td>1</td>';
        echo '<td>-1</td><td>0</td><td>\'\'</td><td>\'0\'</td><td>\'1\'</td><td>\'-1\'</td><td>\'php\'</td></tr>' . PHP_EOL;
        foreach ($data as $i => $y) {
            echo '<tr bgcolor="' . (($i % 2) ? 'white' : '#efefef') . '">';
            echo html::td(var_export($y, true));
            foreach ($data as $x) {
                $value = ($x == $y) ? '|' : '○';
                echo html::td($value);
            }
            echo '</tr>' . PHP_EOL;
        }
        echo '</table>';
    }

}