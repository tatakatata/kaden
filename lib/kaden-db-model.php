<?php
;

class Kaden_DB_Model{
    var $table;
    var $pk;
    var $cx;
    var $sql;

    function __construct($cx){
        $this->cx  = $cx;
        $this->sql = new Kaden_DB_SQL();
        $this->sql->pk    =& $this->pk;
        $this->sql->table =& $this->table;
        $this->pk  = 'id';

        $this->init();
    }

    function init(){}

    function query($sql, $params = array()){ /* also can query(array( $sql, $params )) */
        if( is_array($sql) ){
            $params = $sql[1];
            $sql    = $sql[0];
        }
        return $this->cx->db->query($sql, $params);
    }

    function find($condition, $tail = ''){
        $sth = $this->query( $this->sql->select($condition, $tail) );
        return $sth->fetch();
    }

    function count($condition){
        $sth = $this->query( $this->sql->count($condition) );
        return $sth->fetchColumn();
    }

    function findAll($condition = '', $tail = ''){
        $sth = $this->query( $this->sql->select($condition, $tail) );
        return $sth->fetchAll();
    }

    function create($data = array()){
        $sth = $this->query( $this->sql->insert($data) );
        return $this->cx->db->pdo->lastInsertId();
    }

    function update($condition, $data){
        $sth = $this->query( $this->sql->update($condition, $data) );
        return $sth;
    }

    function delete($condition){
        $sth = $this->query( $this->sql->delete($condition) );
        return $sth;
    }

    /* TODO inflate(), deflate() */
    function inflate(&$values){}
    function deflate(&$values){}
};
