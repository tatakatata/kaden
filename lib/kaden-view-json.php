<?php

;

class Kaden_View_JSON{
    function view($cx){
        header('Content-type: text/javascript;charset=utf-8');
        echo json_encode($cx->res->param);
    }
};



