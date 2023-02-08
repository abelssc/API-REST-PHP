<?php
require CONFIG_PATH."conexion.php";
class PutModel{
    public $table;
    public $dbh;

    function __construct($table)
    {
        $this->table=$table;
        $this->dbh=Conexion::getConexion();
    }
    public function put($id,$sentence_put){
        $stmt=$this->dbh->prepare("UPDATE $this->table SET $sentence_put WHERE id=:id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->rowCount()){
            return "REGISTRO ACTUALIZADO";
        }else{
            return "NO SE ACTUALIZO EL REGISTRO";
        }

    }
}

?>