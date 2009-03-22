<?php

class Kaden_Cookie extends Kaden_Param
{
    var $expire;
    var $path;
    var $domain;
    var $secure;

    function __construct(){
        parent::__construct();
        $this->param  = $_COOKIE;
        $this->expire = array();
    }

    function put(){
        foreach( $this->param as $name => $value ){
            setcookie($name, $value, $this->expire($name), $this->path, $this->domain, $this->secure);
        }
    }

    function expire($name = null, $value = null){
        if( !isset($name) )
            if( isset($this->expire['def']) )
                return $this->expire['def'];
            else
                return;
        if( isset($value) )
            $this->expire[$name] = $value * 60;
        return $this->expire[$name];
    }
}
