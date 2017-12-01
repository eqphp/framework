<?php

return array(
    'phone' => '/^(1(([3,8][0-9])|(4[5,7])|(5[^4])|(7[0,3,6,7,8])))\d{8}$/',
    'email' => '/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/',
    'telephone' => '/^(0\d{2,3})?-?([2-9]\d{6,7})(-\d{1,5})?$/',
    'hot_line' => '/^(400|800)-(\d{3})-(\d{4})?$/',
    'qq' => '/^[1-9]\d{4,9}$/',
    'account' => '/^[a-zA-Z][a-zA-Z0-9_]{4,17}$/',
    'md5' => '/^[a-f0-9]{32}$/',
    'password' => '/^(.){6,18}$/',
    'money' => '/^[0-9]+([.][0-9]{1,2})?$/',
    'number' => '/^\-?[0-9]*\.?[0-9]*$/',
    'numeric' => '/^\d+$/',
    'alpha' => '/^[A-Za-z]+$/',
    'alpha_numeric' => '/^[A-Za-z0-9]+$/',
    'alpha_numeric_dash' => '/^[A-Za-z0-9_]+$/',
    'captcha' => '/^[a-z0-9]{5}$/',
    'message_code' => '/^[1-9]\d{5}$/',
    'url' => '/^http(s?):\/\/([\w-]+\.)+[\w-]+(\/[\w\- \.\/?%&=]*)?/',
    'cid' => '/^\d{15}$|^\d{17}(\d|X|x)$/',
    'zip' => '/^\d{6}$/',
    'address' => '/^(.){0,64}$/',
    'trade_no' => '/^\d{18}$/',
    'int' => '/^[-\+]?\d+$/',
    'float' => '/^[-\+]?\d+(\.\d+)?$/',
    'chinese' => '/^[\x{4e00}-\x{9fa5}]+$/u',
    'chinese_name' => '/^[\x{4e00}-\x{9fa5}]{2,5}$/u',
    'name' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u',
    'file_name' => '/^[^\/\\:*?"<>|,]+$/',
    'id' => '/^[1-9]{1}[0-9]{0,9}$/',
    'uuid' => '/^[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$/i',
    'image' => '/<img[^\/>src]+src="([^"]+)"[^\/>]*\/?/',
    'business_license' => '/^\d{13}$|^\d{14}([0-9]|X|x)$|^\d{6}(N|n)(A|a|B|b)\d{6}(X|x)$/',

);