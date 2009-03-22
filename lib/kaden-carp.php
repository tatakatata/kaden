<?php

;

class Kaden_Carp {
    function __construct(){}

    public static function croak($message){
        $backtrace = debug_backtrace();
        $backtrace = $backtrace[1];
        if( !isset($message) ) $message = 'Died';
        if( !preg_match('/\n$/', $message) )
            $message .= ' at ' . $backtrace[file] . ' line ' . $backtrace[line] . '.' . "\n";
        echo $message;
        exit();
    }

    public static function carp($message){
        $backtrace = debug_backtrace();
        $backtrace = $backtrace[1];
        if( !isset($message) ) $message = 'Died';
        if( !preg_match('/\n$/', $message) )
            $message .= ' at ' . $backtrace[file] . ' line ' . $backtrace[line] . '.' . "\n";
        echo $message;
    }
};
