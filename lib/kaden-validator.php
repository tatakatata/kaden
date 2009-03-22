<?php

class Kaden_Validator
{
    var $profile;
    var $messages;
    var $data;

    function __construct($profile = array(), $messages = array()){
        $this->profile = $profile;
        $this->messages = $messages;
    }

    function param(){
        $data = array();
        foreach( $this->profile as $fieldname => $funcs ){
            if( preg_match('/^_/', $fieldname) )
                continue;
            $data[$fieldname] = $this->data[$fieldname];
        }
        return $data;
    }

    function validate($data){
        $this->data = $data;

        $result = new Kaden_Validator_Result($data);

        foreach( $this->profile as $fieldname => $funcs ){
            if( preg_match('/^_/', $fieldname) )
                continue;

            $field = new Kaden_Validator_Result_Field($this->data[$fieldname]);
            $result->field($fieldname, $field);
            
            if(!isset($this->data[$fieldname]) || $this->data[$fieldname] === ''){
                if(!$this->allow_empty($fieldname)){
                    $field->is_missing = true;
                    $field->message = sprintf($this->messages['prefix'], $this->messages['missing']);//, 'missing');
                }
                continue;
            }

            foreach( $funcs as $func ){
                if( !is_array($func) ){
                    $called = array('Kaden_Validator_Functions', $func);
                    $args   = array();
                } else {
                    $f = key($func);
                    $called = array('Kaden_Validator_Functions', key($func));
                    $args   = $func[$f];
                }
                array_unshift($args, $this->data[$fieldname]);
                if(!call_user_func_array($called, $args)){
                    $field->is_invalid = true;
                    $field->message = sprintf($this->messages['prefix'],
                                              $this->messages['invalid']
                                              //, $called[1]);
                                              );
                    break;
                }
            }
        }

        return $result;
    }

    function allow_empty($fieldname){
        //        print_r($this->profile['_required']);
        if( !in_array($fieldname, $this->profile['_required'] ) )
            return true;
        if( in_array($fieldname, $this->profile['_allow_empty'] ? $this->profile['_allow_empty'] : array() ) )
            return true;
        return false;
    }
};

class Kaden_Validator_Functions
{
    function length($str, $min, $max = 0){
        if(!$max){
            $max = $min;
            $min = 0;
        }
        $len = mb_strlen($str, 'utf8');
        return $min <= $len and $len <= $max;
    }

    function ascii($str){
        return preg_match("/^[\x20-\x7E]+$/", $str);
    }

    function number($str){
        return is_numeric($str);
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

class Kaden_Validator_Result
{
    var $data;
    var $fields;

    function __construct($data = array()){
        $this->data = $data;
        $this->fields = array();
    }

    function field($name, $field = ''){
        if( $field )
            $this->fields[$name] =& $field;
        if( empty($this->fields[$name]) ){
            $dummy = new Kaden_Validator_Result_Field('');
            return $dummy;
        }
        return $this->fields[$name];
    }

    function has_error(){
        foreach($this->fields as $name => $field){
            if( $field->is_error() )
                return true;
        }
        return false;
    }

    function has_invalid(){
        foreach($this->fields as $name => $field){
            if( $field->is_invalid )
                return true;
        }
        return false;
    }

    function has_missing(){
        foreach($this->fields as $name => $field){
            if( $field->is_missing )
                return true;
        }
        return false;
    }
};

class Kaden_Validator_Result_Field
{
    var $is_missing;
    var $is_invalid;
    var $message;
    var $value;

    function __construct($value){
        $this->is_missing = false;
        $this->is_invalid = false;
        $this->message    = '';
        $this->value      = $value;
    }

    function is_missing(){
        return $this->is_missing;
    }

    function is_invalid(){
        return $this->is_invalid;
    }

    function value(){
        return $this->value;
    }

    function valid_value(){
        return $this->is_error() ? '' : $this->value;
    }

    function is_error(){
        return ($this->is_missing or $this->is_invalid);
    }

    function message(){
        return $this->message;
    }
};
