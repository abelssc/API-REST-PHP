<?php
require MODELS_PATH."post.php";
class PostController{
    public $table;
    function __construct($table)
    {
        $this->table=$table;
    }
    public function post($json){
        
    }
}