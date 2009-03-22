<?php
;

class Kaden_Dispatcher
{
    var $dispatch_table;
    function __construct($table){
        $this->dispatch_table = $table;
        if( !isset($this->dispatch_table) )
            $this->dispatch_table = array();
    }

    function generate_regexp($path){
        $regexp = preg_replace('/{(.+?)}/', "(?<$1>.+?)", $path);
        return '{^' . $regexp . '/?$}';
    }

    function dispatch($cx, $pathinfo){
        if( (empty($pathinfo) or $pathinfo === '/') and $this->dispatch_table['_default'] )
            return array(
                         $this->dispatch_table['_default']['controller'],
                         $this->dispatch_table['_default']['action'],
                         array()
                         );

        foreach($this->dispatch_table as $controller => $actions){
            foreach($actions as $action => $paths)
                foreach($paths as $path){
                    $regexp = $this->generate_regexp($path);
                    if( preg_match($regexp, $pathinfo, $match) ){
                        if( $pathinfo === $match[0] and count($match) == 1 ) $match = false;
                        return array($controller, $action, $match);
                    }
                }
        }

        $pieces = array_reverse(explode('/', $pathinfo));
        $file_path = array_shift($pieces);
        foreach($pieces as $piece){
            if( file_exists($file_path) ){
                header(sprintf("Location: %s%s", $cx->url->dir(), $file_path));
                break;
            }
            $file_path = $piece . '/' . $file_path;
        }

        Kaden_Carp::croak("No actions for '<b>$pathinfo</b>'");
    }
};
