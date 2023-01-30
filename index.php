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

##definimos rutas permitidas
$rutas_permitidas=["categorias","productos","clientes","usuarios"];

if(in_array($ruta_solicitada,$rutas_permitidas)){
    ##obtenemos el metodo
    $method= $_SERVER["REQUEST_METHOD"];
    ##incluimos el controlador especifico para ese metodo
    include CONTROLLERS_PATH."$method.php";


    if($method==="GET"){
        
        $class=new GetController($ruta_solicitada);

        ##PETICIONES CON ID ruta/id
        if(!empty($id)){
            echo json_encode($class->getOne($id));
        }
        ##PETICIONES PARA PAGINADO ruta?_limit=""&_page=""
        else if(isset($_GET["_limit"])||isset($_GET["_page"])){
            echo json_encode($class->getWithPaginate($_GET));
        }
        ##PETICIONES CON WHERE page?col1=""&col2=""
        else if(!empty($_GET)){
            echo json_encode($class->getWithFilters($_GET));
        }
        ##PETICIONES PARA OBTENER TODO /ruta
        else{
            echo json_encode($class->getAll());
        }
    }
    else if($method==="POST"){
        // $class=new PostController($route);

    }
}

else{
    echo "ruta no permitida";
}