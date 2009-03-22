<?php
;

class Kaden_Core
{
    var $req;
    var $res;
    var $cookie;
    var $session;
    var $var;
    var $config;
    var $dispatcher;
    var $validate;
    var $db;
    var $model;
    var $validator;
    var $url;

    function __construct(){
        $this->init();
    }

    function init(){
        $this->_load_config();
        $this->_load_dispatcher();

        $this->var     = array();
        $this->model   = array();
        $this->url     = new Kaden_URL;
        $this->req     = new Kaden_Request();
        $this->res     = new Kaden_Response();
        $this->cookie  = new Kaden_Cookie();
        $this->session = new Kaden_Session($this);
        $this->db      = new Kaden_DB($this);
    }

    function _load_config( $config_yaml = 'config.yaml' ){
        if( file_exists($config_yaml) )
            $this->config = Spyc::YAMLLoad($config_yaml);

        $domain_yaml = 'config/' . $_SERVER['SERVER_NAME'] . '.yaml';
        if( file_exists($domain_yaml) )
            $this->config = array_merge($this->config, Spyc::YAMLLoad($domain_yaml));
    }

    function _load_dispatcher( $dispatch_yaml = 'dispatch.yaml' ){
        if( file_exists($dispatch_yaml) ){
            $dispatch_table = Spyc::YAMLLoad($dispatch_yaml);
            $this->dispatcher = new Kaden_Dispatcher($dispatch_table);
        } else 
            Kaden_Carp::carp('file defining dispatching table is not found.');
    }

    function begin(){
        if( isset($this->config['db']) )
            $this->db->connect();

        if( isset($this->config['session']) )
            $this->session->prepare();

        return $this;
    }

    function before_view(){
        $this->res->header['cache-control'] = 'no-cache';
        $this->res->put_header();
        $this->cookie->put();
    }

    function end(){
        if( isset($this->config['session']) )
            $this->session->save();
    }

    function validator($profile){
        if( file_exists('validate.yaml') )
            $this->validate = Spyc::YAMLLoad('validate.yaml');
        else {
            Kaden_Carp::carp('validate.yaml does not exists.');
            return;
        }

        if( !isset($this->validator) ){
            $this->validator = new Kaden_Validator();
            $this->validator->messages = $this->validate['_Messages'];
        }

        if( isset($profile) )
            $this->validator->profile = $this->validate[$profile];

        return $this->validator;
    }

    function dispatch($pathinfo = ''){
        if( $pathinfo === '' and !empty($_SERVER['PATH_INFO']) )
            $pathinfo = $_SERVER['PATH_INFO'];
        return $this->dispatcher->dispatch($this, $pathinfo);
    }

    function run($controller = '', $action = '', $args = array()){
        $this->begin();

        if( !$controller )
            list($controller, $action, $args) = $this->dispatch();

        if(!$args) $args = array();

        $this->req->param += $args;
        $this->call('controller', $controller, $action);
        $this->end();
    }

    function identify_type($type){
        // type: controller, model, view
        if( preg_match('/^c/i', $type ) )
            $type = 'c';
        elseif( preg_match('/^m/i', $type ) )
            $type = 'm';
        elseif( preg_match('/^v/i', $type ) )
            $type = 'v';
        else
            Kaden_Carp::carp('Unknown component\'s type: ' . $type);
        return $type;
    }
    
    function get_components_information($type, $name){
        $type         = $this->identify_type($type);
        $name         = is_array($name) ? $name : array($name);
        $selfclass    = strtolower(get_class($this));
        $include_path = isset($this->config['include_path']) ? 
            $this->config['include_path'] : $selfclass;

        $filename = implode('/', array($include_path, 
                                       $type,
                                       strtolower(implode('-', $name))
                                       )
                            ) . '.php';
        $classnamearray = array($selfclass, $type);
        foreach($name as $n) array_push($classnamearray, $n);
        $classname = implode('_', $classnamearray);

        return array($filename, $classname);
    }

    function call($type, $name, $action, $args = array()){
        list($filename, $classname) = $this->get_components_information($type, $name);

        if(file_exists($filename))
            require_once( $filename );

        $obj;
        eval('$obj = new ' . $classname . '($this);');

        if( method_exists($obj, $action) )
            return call_user_func_array(array($obj, $action), array($this));
        else
            Kaden_Carp::carp('Cannot call ' . get_class($obj) . ' ' . $action);
    }

    function view($name = 'php'){
        $this->before_view();
        $this->call('view', $name, 'view');
    }

    function model($model){
        list($filename, $classname) = $this->get_components_information('model', $model);

        if( !isset($this->model[$filename]) ){
            if(file_exists($filename))
                require_once( $filename );
            $obj;
            if( class_exists($classname) ){
                eval('$obj = new ' . $classname . '($this);');
            } else {
                $obj = new Kaden_DB_Model($this);
                $obj->table = $model;
            }
            $this->model[$filename] = $obj;
        }

        return $this->model[$filename];
    }
};

