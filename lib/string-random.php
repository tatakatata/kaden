<?php

/** 
* String_Random
* PerlのString::RandomのPHP版です。
* 
* 履歴
* 2008/01/18 ver 0.01
* 2008/09/16 ver 0.02
* 
* @author ittetsu miyazaki<ittetsu.miyazaki@gmail.com>
* @version 0.02
* @package String
* @access public
*/
class String_Random {
    
    var $max = 10;
    var $patterns;
    var $regch = array(
        '\\' => '_slash',
        '.'  => '_dot',
        '['  => '_bracket',
        '*'  => '_asterisk',
        '+'  => '_plus',
        '?'  => '_question',
        '{'  => '_brace',
    );
    
    function String_Random($max = null) {
        static $patterns;
        if ( !$patterns ) {
            $upper = range('A','Z');
            $lower = range('a','z');
            $digit = range('0','9');
            $punct_no = array(
                '!','"','#','$','%','&',"'",'(',
                ')','*','+',',','-','.','/',':',
                ';','<','=','>','?','@','[','\\',
                ']','^','`','{','|','}','~'
            );
            $punct = array_merge($punct_no,'_');
            $patterns = array(
                '.'  => array_merge($upper, $lower, $digit, $punct),
                '\d' => $digit,
                '\D' => array_merge($upper, $lower, $punct),
                '\w' => array_merge($upper, $lower, $digit, "_"),
                '\W' => $punct_no,
                '\s' => array(" ", "\t"),
                '\S' => array_merge($upper, $lower, $digit, $punct),
                '\t' => array("\t"),
                '\n' => array("\n"),
                '\r' => array("\r"),
            );
        }
        $this->patterns = $patterns;
        if ( !is_null($max) ) $this->max = $max;
    }
    
    function randregex ($patterns) {
        if ( !is_array($patterns) ) return $this->_randregex($patterns);
        $ret = array();
        foreach ($patterns as $pattern) {
            $ret[] = $this->_randregex($pattern);
        }
        return $ret;
    }
    
    function _randregex ($pattern) {
        
        $string = array();
        $chars  = preg_split('//',$pattern,-1,PREG_SPLIT_NO_EMPTY);
        $i = count($chars);
        
        while ($i--) {
            $ch = array_shift($chars);
            if ( array_key_exists($ch,$this->regch) ) {
                $method = $this->regch[$ch];
                $this->$method($ch,$chars,$string);
            }
            else {
                if ( preg_match("/[\$\^\*\(\)\+\{\}\]\|\?]/", $ch) )
                    trigger_error("'$ch' not implemented.  treating literally.",E_USER_WARNING);
                $string[] = array($ch);
            }
        }
        
        $ret = '';
        foreach ($string as $ch) {
            $ret .= $ch[array_rand($ch)];
        }
        return $ret;
    }
    
    function _slash ($ch,&$chars,&$string) {
        if ( !$chars ) 
            trigger_error("regex not terminated",E_USER_ERROR);
        
        $tmp = array_shift($chars);
        if ( (string)$tmp === 'x' ) {
            $tmp = array_shift($chars) . array_shift($chars);
            $string[] = array(chr(hexdec($tmp)));
        }
        elseif ( preg_match('/[0-7]/',$tmp) ) {
            trigger_error("octal parsing not implemented.  treating literally.",E_USER_WARNING);
            $string[] = array($tmp);
        }
        elseif ( array_key_exists($ch.$tmp,$this->patterns) ) {
            $string[] = $this->patterns[$ch.$tmp];
        }
        else {
            trigger_error("'\\$tmp' being treated as literal '$tmp'",E_USER_WARNING);
            $string[] = array($tmp);
        }
    }
    
    function _dot ($ch,&$chars,&$string) {
        $string[] = $this->patterns[$ch];
    }
    
    function _bracket ($ch,&$chars,&$string) {
        $tmp = array();
        $i = count($chars);
        while ($i--) {
            $ch = array_shift($chars);
            if ( (string)$ch === ']' ) break;
            if ( (string)$ch === "-" && $chars && $tmp ) {
                $ch = array_shift($chars);
                for ( $j = ord($tmp[count($tmp)-1]); $j < ord($ch); $j++ ) {
                    $tmp[] = chr($j+1);
                }
            }
            else {
                if ( preg_match('/\W/',$ch) )
                    trigger_error("'$ch' will be treated literally inside []",E_USER_WARNING);
                $tmp[] = $ch;
            }
        }
        
        if ( (string)$ch !== ']' )
            trigger_error("unmatched []",E_USER_ERROR);
        
        $string[] = $tmp;
    }
    
    function _asterisk ($ch,&$chars,&$string) {
        array_unshift($chars,"{","0",",","}");
    }
    
    function _plus ($ch,&$chars,&$string) {
        array_unshift($chars,"{","1",",","}");
    }
    
    function _question ($ch,&$chars,&$string) {
        array_unshift($chars,"{","0",",","1","}");
    }
    
    function _brace ($ch,&$chars,&$string) {
        if ( !in_array("}",$chars) ) return $string[] = array($ch);
        
        $tmp = '';
        $i = count($chars);
        while ($i--) {
            $ch = array_shift($chars);
            if ( (string)$ch === '}' ) break;
            if ( !preg_match("/[\d,]/", $ch) )
                trigger_error("'$ch' inside {} not supported",E_USER_ERROR);
            $tmp .= $ch;
        }
        
        if ( preg_match("/,/", $tmp) ) {
            if ( !preg_match("/^(\d*),(\d*)$/", $tmp, $matches) ) 
                trigger_error("malformed range {$tmp}",E_USER_ERROR);
            
            $min = strlen($matches[1]) ? $matches[1] : 0;
            $max = strlen($matches[2]) ? $matches[2] : $this->max;
            
            if ( $min > $max )
                trigger_error("bad range {$tmp}",E_USER_ERROR);
           
            if ( (int)$min === (int)$max ) {
                $tmp = $min;
            }
            else {
                $tmp = $min + rand(0,$max - $min);
            }
        }
        
        if ($tmp) {
            $last = $string[count($string)-1];
            for ($i = 0; $i < ($tmp-1); $i++) {
                $string[] = $last;
            }
        }
        else {
            array_pop($string);
        }
    }
}
