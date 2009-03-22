<?php

class Kaden_Controller_DB
{
    var $model;
    var $table;

    function __construct($cx){
        $t = explode('_', get_class($this));
        $table = array_pop($t);
        $this->table = $table;
        $this->model = $cx->model($table);
    }

    function _get_id($cx){
        $id = $cx->req->param['id'];
        if( !isset($id) ){
            $cx->error("'find' needs `id`.");
            return;
        }

        return $id;
    }

    function find($cx){
        if( !($id = $this->_get_id($cx)) )
            return;

        if( !($cx->res->param = $this->model->find($id)) ){
            $cx->error("Not found id: $id.");
            return;
        }

        $cx->view();
    }

    function findAll($cx){
        $cx->res->param = $this->model->findAll();
        $cx->view();
    }

    function create($cx, $q = null){
        /* need to validate in child method.
           if inputs are valid, call this parent::create() */

        if( !isset($q) )
            $q = $cx->req->param;

        if( !($id = $this->model->create($q)) ){
            $cx->error('Something is wrong. Failed to create.');
            return;
        }

        $cx->res->param = $this->model->find($id);
        $cx->view();
    }

    function update($cx, $q = null){
        /* need to validate in child method.
           if inputs are valid, call this parent::update() */

        if( !isset($q) )
            $q = $cx->req->param;

        if( !($id = $this->_get_id($cx)) )
            return;

        if( $this->model->update($id, $q) ){
            $cx->error('Something is wrong. Failed to update.');
            return;
        }

        $cx->res->param = $this->model->find($id);
        $cx->view();
    }
};
