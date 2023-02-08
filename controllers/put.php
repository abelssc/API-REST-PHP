<?php
require MODELS_PATH."put.php";
class PutController{
    public $table;
    public $model;
    public $sentence_put="";

    function __construct($table)
    {
        $this->table=$table;
        $this->model=new PutModel($table);
    }
    public function setJson($json){
        foreach ($json as $key => $value) {
            $this->sentence_put.="$key = '$value', ";
        }
    }
    public function put($id){
        $this->sentence_put=rtrim($this->sentence_put,", ");

        $id=intval($id);
        if($id){
            return $this->model->put($id,$this->sentence_put);
        }
        else{
            return "ID NO PERMITIDO";
        }
    }
}

?>