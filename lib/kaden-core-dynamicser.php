<?php

class Kaden_Core_Dynamicser
{
    var $instances;

    function __construct(){
        $this->instances = array();
    }

    function load($component, $classname = ''){
        if( strpos($component, '+') === 0 )
            $component = substr($component, 1);
        else
            $component = 'kaden-' . $component;
        if( file_exists($component . '.php') )
            require_once($component . '.php');

        if( $classname === '' )
            $classname = preg_replace('/-/', '_', $component);

        eval('$obj = new ' . $classname . '($this);');
        $this->instances[$classname] = $obj;
    }

    function __call($method, $args){
        
    }
};
