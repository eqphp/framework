<?php

class page{

    //返回分页导航,$page_num取值为2至10(备选)
    static function mark($url, $page_count, $page = 1, $show_num = 5){
        $mark_info = '';
        if ($page_count < 2) {
            return $mark_info;
        }

        if ($page_count < 11) {
            for ($i = 1; $i <= $page_count; $i++) {
                if ($i == $page) {
                    $mark_info .= '<a class="current_page">' . $i . '</a>';
                } else {
                    $mark_info .= '<a href="' . $url . $i . '">' . $i . '</a>';
                }
            }
            return $mark_info;
        }

        if ($page > $show_num + 1) {
            $mark_info .= '<a href="' . $url . '1">1</a>';
        }
        if ($page > $show_num + 2) {
            $mark_info .= '<a href="' . $url . ($page - 1) . '">Last</a>';
        }

        $min = $page - $show_num;
        if ($min < 1) {
            $min = 1;
        }
        $max = $page + $show_num;
        if ($max > $page_count) {
            $max = $page_count;
        }

        for ($i = $min; $i <= $max; $i++) {
            if ($i == $page) {
                $mark_info .= '<a class="current_page">' . $i . '</a>';
            } else {
                $mark_info .= '<a href="' . $url . $i . '">' . $i . '</a>';
            }
        }

        if ($page < $page_count - $show_num - 1) {
            $mark_info .= '<a href="' . $url . ($page + 1) . '">Next</a>';
        }
        if ($page < $page_count - $show_num) {
            $mark_info .= '<a href="' . $url . $page_count . '">' . $page_count . '</a>';
        }

        $mark_info .= '<input class="skip_input" type="text" url="' . $url . '" maxlength="4" size="5">';
        return $mark_info;
    }

}