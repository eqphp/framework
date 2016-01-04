<?php
return array(

    'user'=>array(
        'focus'=>'a_follow::index',
        'friend'=>'a_follow::index',
        'fans'=>'a_follow::index',
    ),

    'blog'=>array(
        'list'=>'a_index::get_list',
        'write'=>'a_process::write',
    ),



);