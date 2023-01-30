<?php
    require MODELS_PATH."get.php";
 
    class GetController{
        public $table;
        public $model;

        function __construct($table)
        {     
            $this->table = $table;
            $this->model= new GetModel($this->table);
        }
        public function getAll(){
            return $this->model->getAll();
        }
        public function getOne($id){
            return $this->model->getOne($id);
        }
        public function getWithFilters($filters){
            ##primero creamos la cadena col1=val1 and col2=val2 and ....
            $str="";
            foreach ($filters as $key => $value) {
                $str.="$key = '$value' and "; 
            }
            ##rtrim elimina el ultimo and
            $str=rtrim($str," and ");

            #y enviamos el filtro
            return $this->model->getWithFilters($str);
        }
        public function getWithPaginate($paginate){
            $limit=$paginate["_limit"]??10;
            $page=($paginate["_page"]??0)*$limit;
            return $this->model->getWithPaginate($page,$limit);
        }

    }
