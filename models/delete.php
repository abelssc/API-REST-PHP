<?php
require CONFIG_PATH."conexion.php";
class DeleteModel{
    public $table;
    public $dbh;

    function __construct($table)
    {
        $this->table=$table;
        $this->dbh= Conexion::getConexion();
    }
    public function delete(int $id){
        $stmt=$this->dbh->prepare("DELETE FROM $this->table WHERE id=:id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()){
            return "SE ELIMINO UN REGISTRO DE LA BBDD";
        }else{
            return "SURGIO UN ERROR, NO SE ELIMINO EL REGISTRO DE LA BBDD";
        }

    }

}
?>