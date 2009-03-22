<?php
;

class Kaden_DB_SQL{
    var $table;
    var $pk;
    
    function __construct(){
    }

    function removeSpaces($sql){
        $sql = preg_replace('/^\s+/', '',  $sql);
        $sql = preg_replace('/\s+$/', '',  $sql);
        $sql = preg_replace('/\s+/',  ' ', $sql);
        return $sql;
    }

    function table(){
        return '`' . $this->table . '`';
    }

    function where($condition){
        if(empty($condition)) return array('', array());

        $sql    = 'where ';
        $values = array();
        if( is_array($condition) ){
            $pairs  = array();
            foreach($condition as $name => $value){
                if( is_array($value) ){
                    foreach($value as $ex => $data){
                        if( is_numeric($ex) ){
                            $pairs[]  = "`$name` = ?";
                        } else {
                            $pairs[] = "`$name` $ex ?";
                        }
                        $values[] = $data;
                    }
                } else {
                    $pairs[]  = "`$name` = ?";
                    $values[] = $value;
                }
            }
            $sql .= implode(' and ', $pairs);
        } elseif( is_numeric($condition) ) {
            $sql .= '`' . $this->pk . '` = ?';
            $values[] = $condition;
        } else {
            $sql .= $condition;
        }

        return array($this->removeSpaces($sql), $values);
    }

    function tail($option){
        $sql = '';
        $values = array();

        if( !isset($option) ){
            return array('', $values);
        }

        if( is_array($option) ){
            /*
            $pairs = array();
            foreach( $options as $name => $value ){
                $name    = preg_replace('/_/', ' ', $name);
                $pairs[] = 
            }
            */
        } else {
            $sql = $option;
        }

        return array($this->removeSpaces($sql), $values);
    }

    function select($condition, $option){
        list($tail,  $tailvalues) = $this->tail($option);
        list($where, $values)     = $this->where($condition);
        $sql = "select * from " . $this->table() . " $where $tail";

        return array($this->removeSpaces($sql), array_merge($values, $tailvalues));
    }

    function count($condition){
        list($where, $values) = $this->where($condition);
        $sql = "select count(*) from " . $this->table() . " $where";

        return array($this->removeSpaces($sql), $values);
    }

    function insert($data = array()){
        $names   = array();
        $holders = array();
        $values  = array();

        foreach($data as $name => $value){
            $names[]   = "`$name`";
            $values[]  = $value;
            $holders[] = '?';
        }

        $sql =
            "insert into " . $this->table() .
            " (" . implode(',', $names) . ") values(" . implode(',', $holders) . ")";

        return array($this->removeSpaces($sql), $values);
    }

    function update($condition, $data = array()){
        $pairs  = array();
        $values = array();
        
        foreach($data as $name => $value){
            $pairs[]  = "`$name` = ?";
            $values[] = $value;
        }
        $set = ' set ' . implode(',', $pairs);

        list($where, $wherevalues) = $this->where($condition);

        $sql = "update " . $this->table() . " $set $where";
        return array($this->removeSpaces($sql), array_merge($values, $wherevalues));
    }

    function delete($condition){
        list($where, $values) = $this->where($condition);
        $sql = "delete from " . $this->table() . " $where";
        return array($this->removeSpaces($sql), $values);
    }

};