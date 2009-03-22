<?php
;

class Kaden_DB{
    var $cx;
    var $pdo;
    var $sth;

    function __construct($cx){
        $this->sth = array();
        $this->cx  = $cx;
    }

    function connect(){
        if( !isset($this->pdo) ){
            try {
                $this->pdo = new PDO(
                                     $this->cx->config['db']['dsn'],
                                     $this->cx->config['db']['user'],
                                     $this->cx->config['db']['password']
                                     );
            } catch(PDOExecption $e){
                Kaden_Carp::carp($e->getMessage());
                return false;
            }
        }
    }

    function query($sql, $params = array()){
        if( $this->cx->config['db']['debug'] ){
            echo '<pre>';
            echo $sql, "\n";
            print_r($params);
            echo '</pre>';
        }
        if( !isset($this->sth[$sql]) ){
            $this->sth[$sql] = $this->pdo->prepare($sql);
        }
        try{
            $this->sth[$sql]->execute( $params );
        } catch(PDOExecption $e){
            Kaden_Carp::carp($e->getMessage());
            return false;
        }
        //$this->sth[$sql]->setFetchMode(PDO::FETCH_ASSOC);
        $this->sth[$sql]->setFetchMode(PDO::FETCH_BOTH);

        return $this->sth[$sql];
    }
};

