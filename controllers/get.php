<?php
require MODELS_PATH . "get.php";

class GetController
{
    public $table;
    public $model;
    public $sentence_filter="";
    public $sentence_order="";
    public $sentence_limit="";

    function __construct($table)
    {
        $this->table = $table;
        $this->model = new GetModel($this->table);
    }
    public function getAll()
    {
        return $this->model->getAll();
    }
    public function getOne($id)
    {
        return $this->model->getOne($id);
    }
    public function getWithFilters($filters)
    {
        ##primero creamos la cadena col1=val1 and col2=val2 and ....
        $str = "";
        foreach ($filters as $key => $value) {
            $str .= "$key = '$value' and ";
        }
        ##rtrim elimina el ultimo and
        $str = rtrim($str, " and ");

        #y enviamos el filtro
        return $this->model->getWithFilters($str);
    }
    public function getWithPaginate($paginate)
    {
        $limit = $paginate["_limit"] ?? 10;
        $page = ($paginate["_page"] ?? 0) * $limit;
        return $this->model->getWithPaginate($page, $limit);
    }
    //FILTROS
    public function filter_include($filters)
    {
        $key=$filters["key"];
        $values=$filters["array_values"];
        $sentence="";
        foreach ($values as $value) {
            $sentence.= "$key = '$value' or ";
        }
        $sentence=rtrim($sentence," or ");
        $this->sentence_filter .= "$sentence and ";
        
    }
    public function filter_exclude($filters)
    {
        $key=$filters["key"];
        $values=$filters["array_values"];
        foreach ($values as $value) {
            $this->sentence_filter .= "$key <> '$value' and ";
        }
    }

    public function filter_like($filters)
    {
        $key=$filters["key"];
        $values=$filters["array_values"];
        foreach ($values as $value) {
            $this->sentence_filter .= "$key LIKE '%$value%' and ";
        }
    }
    public function filter_gte($filters){
        $key=$filters["key"];
        $values=$filters["array_values"];
        foreach ($values as $value) {
            $this->sentence_filter .= "$key >= $value and ";
        }
    }
    public function filter_lte($filters){
        $key=$filters["key"];
        $values=$filters["array_values"];
        foreach ($values as $value) {
            $this->sentence_filter .= "$key <= $value and ";
        }
    }
    public function order($order){
        $col=$order["col"];
        $sort=$order["sort"];
        if(empty($this->sentence_order)){
            $this->sentence_order="ORDER BY ";
        }
        $this->sentence_order .= "$col $sort , ";
    }
    public function limit($limit){
        $limit = $paginate["_limit"] ?? 10;
        $page = ($paginate["_page"] ?? 0) * $limit;
        return $this->model->getWithPaginate($page, $limit);
    }
    public function getFilters(){
        $this->sentence_filter=rtrim($this->sentence_filter," and ");
        $this->sentence_order=rtrim($this->sentence_order," , ");
        return $this->model->getFilters($this->sentence_filter,$this->sentence_order,$this->sentence_limit);
    }
 
    //ORDEN
    // public function orderBy($orders){
    //     $sort=$orders["sort"];
    //     $order=$orders["order"];//DESC OR ASC
    //     $this->sentence_order="ORDER BY $sort $order ,";
    // }
}
