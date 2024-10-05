<?php

class Conexao {
    
    private $host = '192.168.1.4';
    private $dbname = 'laygeladinhos';
    private $user = 'developer';
    private $pass = 'Leo140707';

    public function conectar(){
        try{

            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->pass"
            );

            return $conexao;

        }catch(PDOException $e){
            echo '<p>' .$e->getMessage(). '</p>';
        }
    }

}

?>