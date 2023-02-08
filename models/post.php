<?php
include_once CONFIG_PATH."conexion.php";
class PostModel{
    public $table;
    public $dbh;
    public function __construct($table)
    {
        $this->table=$table;
        $this->dbh=Conexion::getConexion();
    }
    public function post($columnas,$valores){
        $stmt=$this->dbh->prepare("INSERT INTO $this->table ($columnas) VALUES ('$valores')");
        $stmt->execute();
        if($stmt->rowCount()){
            return "SE  EL REGISTRO EN LA BBDD";
        }else{
            return "ERROR, NO SE INSERTO EL REGISTRO LA BBDD";
        }
    }
}