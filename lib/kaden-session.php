<?php

require_once('string-random.php');

class Kaden_Session extends Kaden_Param
{
    var $cx;
    var $id;
    var $model;
    var $name;
    function __construct($cx){
        $this->cx = $cx;
        parent::__construct();
    }

    function prepare(){
        if( empty($this->cx->config['session']['model']) ){
            Kaden_Carp::carp('config.session.model is not set');
            return;
        }
        if( empty($this->cx->config['session']['name']) ){
            Kaden_Carp::carp('config.session.name is not set');
            return;
        }
        $this->model = $this->cx->model($this->cx->config['session']['model']);
        $this->name  = $this->cx->config['session']['name'];

        $this->id = $this->_get_id();
        $session  = $this->model->find($this->id);
        $session['accessed_on'] = new DateTime($session['accessed_on']);

        $now = new DateTime();
        if($session['accessed_on']->modify( $this->cx->config['session']['expire'] ) < $now){ /* session expiring process */
            $this->id = $this->_new_id();
            $session  = $this->model->find($this->id);
            $session['accessed_on'] = $now;
        }

        $this->param = json_decode($session['param']);

        $this->cx->cookie->param($this->name, $this->id);
    }

    function save(){
        $now   = new DateTime();
        $this->model->update($this->id,
                             array(
                                   param       => json_encode( $this->param ),
                                   accessed_on => $now->format(DATE_DATETIME)
                                   )
                             );
    }

    function _new_id(){
        $random   = new String_Random();
        $id    = $random->randregex('[A-Za-z0-9]{32}');
        $now   = new DateTime();

        $this->model->create( array( id => $id, param => '{}', accessed_on => $now->format(DATE_DATETIME) ) );
        return $id;
    }

    function _get_id(){
        if( $this->cx->req->param($this->name) )
            return $this->cx->req->param($this->name);
        if( $this->cx->cookie->param($this->name) )
            return $this->cx->cookie->param($this->name);
        return $this->_new_id();
    }
}