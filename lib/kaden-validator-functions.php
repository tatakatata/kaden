<?php

class Kaden_Validator_Functions
{
    static $messags =
        array(
              invalid  =>  '入力が正しくありません',
              missing  =>  '入力がありません',
              length   =>  '入力が長すぎるか短すぎます',
              maxlen   =>  '入力が長すぎます',
              minlen   =>  '入力が短すぎます',
              katakana =>  'カタカナで入力してください',
              zipcode  =>  '正しくない郵便番号です',
              ascii    =>  '半角英数記号で入力してください'
              );

    function message($fieldname){
        return Kaden_Validator_Functions::$messags[$fieldname];
    }

    function length($str, $min, $max = 0){
        if(!$max){
            $max = $min;
            $min = 0;
        }
        $len = mb_strlen($str, 'utf8');
        return $min <= $len and $len <= $max;
    }

    function maxlen($str, $max){
        return mb_strlen($str, 'utf8') <= $max;
    }

    function minlen($str, $min){
        return $min <= mb_strlen($str, 'utf8');
    }

    function ascii($str){
        return preg_match("/^[\x20-\x7E]+$/", $str);
    }

    function numeric($str){
        return is_numeric($str);
    }

    function number($str){
        return preg_match('/^[0-9]+$/', $str);
    }

    function regexp($str, $regexp){
        return preg_match($regexp, $str);
    }

    function katakana($str){
        mb_regex_encoding('utf8');
        return mb_ereg("^[ァ-ンー]+$", $str);
    }

    function hiragana($str){
        mb_regex_encoding('utf8');
        return mb_ereg("^[ぁ-んー]+$", $str);
    }

    function zipcode($str){
        return preg_match('/^\d{3}-\d{4}$/', $str);
    }
};

