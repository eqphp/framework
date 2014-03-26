<?php

class p_block{

    //block case
    function test($params,$content,&$smarty,&$repeat){
        if (!$repeat) {
            $v=tpl::get($smarty,'ask');
            return $params['ok'].":".$content.$v;
        }
    }


}