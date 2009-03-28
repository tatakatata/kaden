<?php

class Dynamixer
{
    var $instances = array();
    var $methods   = array();

    function __construct(){}

    function _calling_instance(){
        $backtrace = debug_backtrace();
        $backtrace = $backtrace[2];
        return $backtrace['object'];
    }

    function load($component, $classname = ''){
        if( file_exists($component . '.php') )
            require_once($component . '.php');

        if( $classname === '' )
            $classname = preg_replace('/-/', '_', $component);

        return new $classname();
    }

    function set_instance($instance, $name = null){
        if( is_null($name) ){
            $name = $instance;
            $instance = $this->_calling_instance();
        }
        if( isset($this->instances[$name]) ){
            Kaden_Carp::carp('instance name: `' + $name + '` is used');
            return false;
        }
        $this->instances[$name] = $instance;
        return true;
    }

    function get_instance($name){
        if( $this->instances[$name] )
            return $this->instances[$name];
        else
            Kaden_Carp::croak('called undefined property: `' . $name . '`');
    }

    function assign_method($method, $name = null){
        if( !is_array($method) ){
            $instance = $this->_calling_instance();
        } else {
            $instance = $method[0];
            $method   = $method[1];
        }

        if( is_null($name) )
            $name = $method;

        if( !isset($this->methods[$name]) )
            $this->methods[$name] = array();

        array_push($this->methods[$name], array( $instance, $method ));
        return true;
    }

    function call_assigned_method($name, $args = array()){
        if( !isset($this->methods[$name]) )
            Kaden_Carp::croak('called undefined method: `' . $name . '`');

        foreach($this->methods[$name] as $method)
            $r = call_user_func_array($method, $args);
        return $r;
    }

};

