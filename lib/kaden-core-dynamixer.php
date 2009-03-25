<?php

class Kaden_Core_Dynamixer
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

        eval('$instance = new ' . $classname . '($this);');
        $this->set_instance($classname, $instance);
    }

    function set_instance($name, $instance){ // $cx->set_intance('hoge', $instance);
        if( !(isset($name) and isset($instance)) )
            return false;
        if( isset($this->instances[$name]) )
            return false; /* if $name is already used, return false. */
        if( method_exsists($this, $name) )
            return false;

        $this->instances[$name] =& $instance;

        return true;
    }

    function __get($name){
        if( $this->instances[$name] )
            return $this->instances[$name];
        else
            Kaden_Carp::carp('called undefined property: `' . $name . '`');
    }
};
