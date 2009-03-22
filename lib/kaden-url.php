<?php

class Kaden_URL
{
    function protocol(){
        if( $this->server('PORT') === 443 ) return 'https';
        return 'http';
    }

    function dir(){
        return $this->server_root() . $this->script_dir();
    }

    function server_root(){
        return sprintf('%s://%s',
                       $this->protocol(), $this->server('NAME')
                       ) . ($this->server('PORT') != 80 ? ':'.$this->server('PORT') : '');
    }

    function self($opt = array()){ /* return url beginning from 'http(s)://' */
        $url = $this->server_root() . $this->script_name();
        if( $opt['path_info'] )
            $url .= $this->path_info();
        if( $opt['query_string'] )
            $url .= $this->query_string();
        return $url;
    }

    function path_info(){
        return $_SERVER['PATH_INFO'];
    }

    function query_string(){
        return $_SERVER['QUERY_STRING'];
    }

    function script_name(){
        return $_SERVER['SCRIPT_NAME'];
    }

    function script_dir(){
        return preg_replace('{/[^/]*$}', '/', $this->script_name());
    }

    function server($name){
        return $_SERVER['SERVER_' . strtoupper($name)] ?
            $_SERVER['SERVER_' . strtoupper($name)] : $_SERVER[strtoupper($name)];
    }
};
