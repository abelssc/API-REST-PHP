<?php
require MODELS_PATH."delete.php";
class DeleteController{
    public $table;
    public $model;

    function __construct($table)
    {
        $this->table=$table;
        $this->model=new DeleteModel($this->table);
    }
    public function delete($id){
        $id=intval($id);

        if($id){
            return $this->model->delete($id);
        }
        else{
            return "ID NO PERMITIDO";
        }

    }
}


?>