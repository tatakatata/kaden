<?php

class Kaden_Request extends Kaden_Param{
    function __construct(){
        parent::__construct();
        $this->param = $_POST + $_GET;
    }
};
