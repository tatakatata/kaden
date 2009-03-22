<?php

class Kaden_Param
{
    var $param;

    function __construct(){
        $this->param = array();
    }

    function param($name, $value = null){
        if( !isset($name) )
            return $this->param;
        if( isset($value) )
            $this->param[$name] = $value;
        return $this->param[$name];
    }
};
