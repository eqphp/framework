<?php

class form{

    static function select($name = '', $data = array(), $attribute = '', $value = ''){
        $attribute = self::_parse_attribute($attribute);
        $from = '<select' . $attribute . ' name="' . $name . '">' . PHP_EOL;
        if ($data && is_array($data)) {
            foreach ($data as $k => $v) {
                $from .= '<option value="' . $k . '"';
                if ($value !== '' && $value === $k) {
                    $from .= ' selected';
                }
                $from .= '>' . $v . '</option>' . PHP_EOL;
            }
        }
        return $from . '</select>';
    }

    static function checkbox($name = '', $data = array(), $attribute = '', $value = ''){
        $attribute = self::_parse_attribute($attribute);
        is_string($value) and $value = explode('|', $value);
        $from = '';
        if ($data && is_array($data)) {
            foreach ($data as $k => $v) {
                $from .= '<label><input' . $attribute . ' name="' . $name . '[]" type="checkbox" value="' . $k . '"';
                if ($k && in_array($k, $value, true)) {
                    $from .= ' checked';
                }
                $from .= ' />' . $v . '</label>' . PHP_EOL;
            }
        }
        return $from;
    }

    static function radio($name = '', $data = array(), $attribute = '', $value = ''){
        $attribute = self::_parse_attribute($attribute);
        $from = '';
        if ($data && is_array($data)) {
            foreach ($data as $k => $v) {
                $from .= '<label><input' . $attribute . ' name="' . $name . '" type="radio" value="' . $k . '"';
                if ($value !== '' && $k === $value) {
                    $from .= ' checked';
                }
                $from .= ' />' . $v . '</label>' . PHP_EOL;
            }
        }
        return $from;
    }

    static function textarea($name = '', $data = '', $attribute = ''){
        $attribute = self::_parse_attribute($attribute);
        return '<textarea' . $attribute . ' name="' . $name . '">' . $data . '</textarea>';
    }

    static function __callStatic($name, $param){
        if (in_array($name, array('text', 'password', 'submit', 'button', 'reset', 'hidden', 'file'))) {
            $attribute = self::_parse_attribute($param[2]);
            return '<input' . $attribute . ' name="' . $param[0] . '" type="' . $name . '" value="' . $param[1] . '" />';
        }
    }

    static function open($name = '', $action = '', $attribute = '', $csrf = ''){
        $attribute = self::_parse_attribute($attribute);
        $html = '<form' . $attribute . ' name="' . $name . '" action="' . $action . '">';
        $csrf and $html .= '<input name="eqphp_csrf_token" type="hidden" value="' . $csrf . '">';
        return $html;
    }

    static function close(){
        return '</form>';
    }

    static function captcha($name, $title = '看不清？点击更换', $attribute = ''){
        $attribute = self::_parse_attribute($attribute);
        $src = route('captcha', array('check' => $name, 'random' => ''));
        return '<img' . $attribute . ' src="' . $src . '" alt="' . $title . '" title="' . $title . '">';
    }

    private static function _parse_attribute($attribute){
        $html = '';
        if (is_string($attribute) && strpos($attribute, '|')) {
            if (strpos($attribute, '=')) {
                $attribute = explode('|', $attribute);
                foreach ($attribute as $data) {
                    list($key, $value) = explode('=', $data);
                    $html .= ' ' . $key . '="' . $value . '"';
                }
            } else {
                list($id, $class) = explode('|', $attribute);
                $id and $html .= ' id="' . $id . '"';
                $class and $html .= ' class="' . $class . '"';
            }
            return $html;
        } elseif (is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $html .= ' ' . $key . '="' . $value . '"';
            }
        }
        return $html;
    }

}