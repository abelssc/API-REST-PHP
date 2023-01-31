<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/chatApp/config/dirs.php";
##Obtenemos la url /chatApp/chats/option
$url_actual=parse_url($_SERVER["REQUEST_URI"]);

##Eliminamos las / al inicio y al final de la url
$ruta = trim($url_actual["path"], '/');

##Obtenemos los fragmentos de la ruta
$fragmentos_de_ruta=explode("/",$ruta);
$ruta_solicitada=$fragmentos_de_ruta[1];
$id=$fragmentos_de_ruta[2]??null;

##Obtenemos los valores de la ruta query
##Hacemos esto porque la ruta query puede repetir nombres de clave, y la superglobal $_GET sobreescribe si hay algun duplicado 
if(isset($url_actual["query"])){
    $_GET=explode("&",$url_actual["query"]);
    $params = [];
    foreach ($_GET as $value) {
        $queryParts = explode("=",$value);
        $key=$queryParts[0];
        $value=$queryParts[1];
        if(!isset($params[$key])){
            $params[$key]=[];
        }
        $params[$key][]=$value;
    }
    $_GET=$params;
}

##definimos rutas permitidas
$rutas_permitidas=["categorias","productos","clientes","usuarios"];

if(in_array($ruta_solicitada,$rutas_permitidas)){
    ##obtenemos el metodo
    $method= $_SERVER["REQUEST_METHOD"];
    ##incluimos el controlador especifico para ese metodo
    include CONTROLLERS_PATH."$method.php";


    if($method==="GET"){
        
        $class=new GetController($ruta_solicitada);

        ##PETICIONES CON /RUTA[/ID]?
        if(empty($_GET)){
            echo 
            (empty($id))
            ?json_encode($class->getAll())
            :json_encode($class->getOne($id));
        }
        ##PETICIONES CON ? GET
        else{
            $patterns=[
                "filter"=>"/^.*$/",
                "filter_exclude"=>"/^.*(_ne)$/",
                "filter_like"=>"/^.*(_like)$/",
                "join"=>"/^_embed$/",
                "order_sort"=>"/^_sort$/",
                "order_order"=>"/^_order$/",
                "paginate_limit"=>"/^_limit$/",
                "paginate_page"=>"/^_page$/"
            ];

            foreach ($_GET as $key => $array_values) {
                //ORDEN
                // if(preg_match($patterns["order_sort"],$key)){
                //     $order=["sort"=>$array_values];

                //     $class->orderBy([
                //         "sort"=>$array_values,

                //         "order"=>$array_values
                //     ]);
                // }

                //FILTROS
                if(preg_match($patterns["filter_exclude"],$key)){
                    $key=rtrim($key,"_ne");
                    $class->filter_exclude([
                        "key"=>$key,
                        "array_values"=>$array_values
                    ]);
                }
                else if(preg_match($patterns["filter_like"],$key)){
                    $key=rtrim($key,"_like");
                    $class->filter_like([
                        "key"=>$key,
                        "array_values"=>$array_values
                    ]);
                }else{
                    $class->filter_include([
                        "key"=>$key,
                        "array_values"=>$array_values
                    ]);
                }
            }
            echo json_encode($class->getFilters());
        
            
            

            // if (isset($_GET["_limit"])||isset($_GET["_page"]))
            // $class->getWithPaginate([
            //     "_limit"=>$_GET["_limit"]??10,
            //     "_page"=>$_GET["_page"]??0
            // ]);
            // if(!empty(preg_grep("/^.*(_ne)$/", array_keys($_GET)))){

            // }
        }
        // if(!empty($id)){
        //     echo json_encode($class->getOne($id));
        // }else if(empty($_GET)){
        //     echo json_encode($class->getAll());
        // }
        // ##PETICIONES PARA GETS
        // ##PETICIONES PARA PAGINADO ruta?_limit=""&_page=""
        // else if(isset($_GET["_limit"])||isset($_GET["_page"])){
        //     echo json_encode($class->getWithPaginate($_GET));
        // }
        // ##PETICIONES CON WHERE page?col1=""&col2=""
        // else if(!empty($_GET)){
        //     echo json_encode($class->getWithFilters($_GET));
        // }
        // ##PETICIONES PARA OBTENER TODO /ruta
        // else{
        //     echo json_encode($class->getAll());
        // }
    }
    else if($method==="POST"){
        // $class=new PostController($route);

    }
}
else{
    echo "ruta no permitida";
}