<?php

class Kaden_Response extends Kaden_Param{
    var $header;

    function __construct(){
        parent::__construct();
        $this->header = array();
    }

    function put_header(){
        foreach($this->header as $name => $value){
            $nameparts = explode('_', $name);
            //foreach($nameparts as &$namepart)
            //    $namepart = ucfirst( strtolower($namepart) );
            $name = implode('-', $nameparts);
            header("$name: $value", false);
        }
    }
};
