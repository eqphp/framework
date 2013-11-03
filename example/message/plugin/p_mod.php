<?php

class p_mod{

static function message_parse_access($access){
if ($access) {
$access_option=explode(',',$access);
$access_list=config('access_list','message_admin');

return s_extend::arr_act($access_option,$access_list,'&#13;');
}
}





}