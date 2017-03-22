<?php

return array(

    //日志级别
    'level'=>array('info','notice','error','alert'),

    //保存模式：单一(single)、一起(mingle)
    'store_mode'=>'single',

    //是否分组保存
    'is_module_save'=>true,

    //均不保存(0),全部保存(1),指定level中的部分值array('info')
    'store_level'=>1,

    //报警模式：不报警(null)、发邮件(email)、发手机短信(message)、发邮件并发短信(both)
    'alarm'=>array(
        'mode'=>null,
        'title'=>'EQPHP system alarm',
        'email'=>'2581221391@qq.com',
        'phone'=>'1866677****',
    ),

);