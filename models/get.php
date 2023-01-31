<?php
require CONFIG_PATH."conexion.php";
class GetModel{
    public $table;
    public $dbh;

    public function __construct($table)
    {
        $this->table = $table;
        $this->dbh = Conexion::getConexion();
    }

    public  function getAll()
    {
        $stmt = $this->dbh->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public  function getOne($id)
    {
        $stmt = $this->dbh->prepare("SELECT * FROM $this->table WHERE id='$id'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function getWithFilters($str){
        $stmt= $this->dbh->prepare("SELECT * FROM $this->table WHERE $str");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getWithPaginate($page,$limit){
        $stmt=$this->dbh->prepare("SELECT * FROM $this->table LIMIT $page,$limit");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function getFilters($sentence_filter){
        $stmt=$this->dbh->prepare("SELECT * FROM $this->table WHERE $sentence_filter");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
