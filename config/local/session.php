<?php

return array(
    //名称
    'name' => 'eqphp_session',

    //只读模式
    'mode' => true,

    //存储方式：files|user|memcached|redis
    'storage' => 'files',

    //存储地址：tcp://127.0.0.1:(11211/6379）
    'path' => PATH_CACHE . 'session',

    //周期：秒
    'expire' => 1440,
);