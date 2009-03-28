<?php

/**
 * 
 * Dynamixer
 *
 * This module supports to create dynamic extended classes.
 *
 * @author <tatakatata@gmail.com>
 *
 * Synopsis
 *
 * <code>
 * class ExtendedClass
 * {
 *     var $dynamixer;
 *     function __construct(){ $this->dynamixer = new Dynamixer; }
 *     function __get($name){ return $this->dynamixer->get_instance($name); }
 *     function __call($name, $args){
 *         return $this->dynamixer->call_assigned_method($name, $args);
 *     }
 * }
 * </code>
 *
 *
 */

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
            Carp::carp('instance name: `' + $name + '` is used');
            return false;
        }
        $this->instances[$name] = $instance;
        return true;
    }

    function get_instance($name){
        if( $this->instances[$name] )
            return $this->instances[$name];
        else
            Carp::croak('called undefined property: `' . $name . '`');
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

        array_unshift($this->methods[$name], array( $instance, $method ));
        return true;
    }

    function call_assigned_method($name, $args = array()){
        if( !isset($this->methods[$name]) )
            Carp::croak('called undefined method: `' . $name . '`');

        reset($this->methods[$name]);
        $r = call_user_func_array(current($this->methods[$name]), $args);
        return $r;
    }

    function next_method(){
        $backtrace = debug_backtrace();

        foreach($this->methods as $name => $methods){
            $method = current($methods);
            if( $method[0] === $backtrace[1]['object'] and $method[1] === $backtrace[1]['function'] ){
                $next = next($methods);
                break;
            }
        }

        if( $next )
            $r = call_user_func_array($next, $backtrace[0]['args']);

        return $r;
    }
};

