<?php

//rely on: image
class picture{

    //返回验证码图片
    static function code($text, $width = 72, $height = 36){
        //设置head信息，输出图片
        header("Content-Type:image/gif");
        $color = array('f9fdfe', 'f2f8f1', 'fef3f1');
        $img = image::create($width, $height, $color[rand(0, 2)]);

        //设置调色板
        $red = image::color($img, 'ff836e');
        $green = image::color($img, 'b9e62a');
        $blue = image::color($img, '6c85fd');
        $color = array($red, $green, $blue);

        //绘制文本
        for ($i = 0; $i < 5; $i++) {
            imagettftext($img, rand(15, 32), rand(-45, 45), 15 * $i + 3, rand(18, $height - 18) + 10, $color[rand(0, 2)], DATA_FONT . 'lucon.ttf', $text[$i]);
        }

        //绘制模糊像素点
        for ($i = 0; $i < 60; $i++) {
            imagesetpixel($img, rand(0, $width), rand(0, $height), $color[rand(0, 2)]);
        }

        //绘制线条、矩形框
        image::draw($img, $color[rand(0, 2)], array(rand(0, $width), rand(0, $height), rand($width / 2, $width), rand($height / 2, $height)), 'line');
        image::draw($img, $color[rand(0, 2)], array(rand(0, $width - 15), rand(0, $height - 15), rand(15, $width), rand(15, $height)), 'rectangle');

        //绘制弧线
        $p1 = array(rand(-$width, $width), rand(-$height, $height), rand(30, $width * 2), rand(20, $height * 2), rand(0, 360), rand(0, 360));
        $p2 = array(rand(-$width, $width), rand(-$height, $height), rand(30, $width * 2), rand(20, $height * 2), rand(0, 360), rand(0, 360));
        image::draw($img, $color[rand(0, 2)], $p1, 'arc');
        image::draw($img, $color[rand(0, 2)], $p2, 'arc');

        //输出图片、清掉画布
        imagegif($img);
        imagedestroy($img);
    }

    //添加水印
    static function mark($picture, $mark = array('http://127.0.0.1', 'water mark'), $file_name = array('test.png', 'file/create/')){
        $image = image::open($picture);
        $img_width = image::info($image, 'width');
        $img_height = image::info($image, 'height');
        $text_color = image::color($image, 'fefefe');
        $alpha = imagecolorallocatealpha($image, 15, 15, 15, 85);
        $border_color = image::color($image, 'efefef');
        image::draw($image, $border_color, array(0, 0, $img_width - 1, $img_height - 1), 'rectangle');
        image::fill($image, $alpha, array(1, $img_height - 35, $img_width - 2, $img_height - 2), 'rectangle');
        imagettftext($image, 9, 0, $img_width - 145, $img_height - 14, $text_color, DATA_FONT . 'lucon.ttf', $mark[0]);
        imagettftext($image, 9, 0, $img_width - 210, $img_height - 15, $text_color, DATA_FONT . 'msyh.ttf', $mark[1]);
        return image::save($image, $file_name[0], $file_name[1]);
    }

    //按照指定尺寸缩放图片
    static function zoom($picture, $file_name = array('eq80_test.png', 'file/create/'), $length = 320, $is_height = false){
        $image = image::open($picture);
        $start_width = image::info($image, 'width');
        $start_height = image::info($image, 'height');

        $end_width = $length;
        $end_height = round(($start_height * $end_width) / $start_width);
        if ($is_height) {
            $end_width = round(($start_width * $length) / $start_height);
            $end_height = $length;
        }

        $canvas = image::create($end_width, $end_height, 'FFFFFF', true);
        $param = array(0, 0, 0, 0, $end_width, $end_height, $start_width, $start_height);
        image::copy($canvas, $image, $param, 4);
        image::save($canvas, $file_name[0], $file_name[1]);

        return $file_name[1] . $file_name[0];
    }

    //生成指定尺寸的用户头像
    static function avatar($image, $true_size, $size_data = array(100, 50, 32), $save_name = 'avatar', $save_dir = ''){
        foreach ($size_data as $target_size) {
            $target_img = image::create($target_size, $target_size, 'FFFFFF', true);
            $param = array(0, 0, 0, 0, $target_size, $target_size, $true_size, $true_size);
            $copy_result = image::copy($target_img, $image, $param, 5);
            if ($copy_result) {
                image::save($target_img, $save_name . '.gif', $save_dir . $target_size . '/');
            }
        }
    }

}