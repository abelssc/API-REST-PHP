<?php
require MODELS_PATH."post.php";

class PostController{
    public $table;
    public $model;

    function __construct($table)
    {
        $this->table=$table;
        $this->model=new PostModel($this->table);
    }
    public function setJson($json){
        $columnas=implode(",",array_keys($json));
        $valores=implode("','",array_values($json));
        return $this->model->post($columnas,$valores);
    }
}