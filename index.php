<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/chatApp/config/dirs.php";
##Obtenemos la url /chatApp/chats/option
$url_actual = parse_url($_SERVER["REQUEST_URI"]);

##Eliminamos las / al inicio y al final de la url
$ruta = trim($url_actual["path"], '/');

##Obtenemos los fragmentos de la ruta
$fragmentos_de_ruta = explode("/", $ruta);
$ruta_solicitada = $fragmentos_de_ruta[1];
$id = $fragmentos_de_ruta[2] ?? null;


##definimos rutas permitidas
$rutas_permitidas = ["categorias", "productos", "clientes", "usuarios"];

if (in_array($ruta_solicitada, $rutas_permitidas)) {
    ##obtenemos el metodo
    $method = $_SERVER["REQUEST_METHOD"];
    ##incluimos el controlador especifico para ese metodo
    include CONTROLLERS_PATH . "$method.php";


    if ($method === "GET") {
        //Obtenemos los valores de la ruta query
        //Hacemos esto porque la ruta query puede repetir nombres de clave, y la superglobal $_GET sobreescribe si hay algun duplicado
        //?ESTE ES EL CODIGO MAS DIFICIL DE LEER
        //?Se deberia mejorar
        //url_actual[query]=col1=val1&col2=val2&col3=val3....
        if (isset($url_actual["query"])) {
            $_GET = explode("&", $url_actual["query"]);
            //$_GET=[col1=val1,col2=val2,...]
            $params = [];
            foreach ($_GET as $value) {
                $queryParts = explode("=", $value);
                $key = $queryParts[0];
                $value = $queryParts[1];
                //existe una columna $key en el arreglo asociativo $params?
                if (!isset($params[$key])) {
                    $params[$key] = [];
                }
                $params[$key][] = $value;
            }
            $_GET = $params;
        }
        /*RETURN
        $_GET=[
            key1=>[val1,val2,...],
            key2=>[...]
        ]
        */
        $class = new GetController($ruta_solicitada);

        ##PETICIONES CON /RUTA[/ID]?
        if (empty($_GET)) {
            echo (empty($id))
                ? json_encode($class->getAll())
                : json_encode($class->getOne($id));
        }
        ##PETICIONES CON ? GET
        else {
            $patterns = [
                "filter" => "/^.*$/",
                "filter_exclude" => "/^.*(_ne)$/",
                "filter_like" => "/^.*(_like)$/",
                "filter_gte" => "/^.*(_gte)$/",
                "filter_lte" => "/^.*(_lte)$/",
                "join" => "/^_embed$/",
                "order" => "/^_order$/",
                "page" => "/^_page$/",
                "limit" => "/^_limit$/"
            ];

            foreach ($_GET as $key => $array_values) {

                //EXCLUDE
                if (preg_match($patterns["filter_exclude"], $key)) {
                    $key = rtrim($key, "_ne");
                    $class->filter_exclude([
                        "key" => $key,
                        "array_values" => $array_values
                    ]);
                }
                //LIKE
                else if (preg_match($patterns["filter_like"], $key)) {
                    $key = rtrim($key, "_like");
                    $class->filter_like([
                        "key" => $key,
                        "array_values" => $array_values
                    ]);
                }
                //GREATER THAN EQUAL
                else if (preg_match($patterns["filter_gte"], $key)) {
                    $key = rtrim($key, "_gte");
                    $class->filter_gte([
                        "key" => $key,
                        "array_values" => $array_values
                    ]);
                }
                //LOW THAN EQUAL
                else if (preg_match($patterns["filter_lte"], $key)) {
                    $key = rtrim($key, "_lte");
                    $class->filter_lte([
                        "key" => $key,
                        "array_values" => $array_values
                    ]);
                }
                //ORDER 
                else if (preg_match($patterns["order"], $key)) {
                    foreach ($array_values as  $value) {
                        $conjunto = explode(",", $value);
                        $col = $conjunto[0];
                        $sort = $conjunto[1];

                        $class->order([
                            "col" => $col,
                            "sort" => $sort
                        ]);
                    }
                }
                //PAGE
                else if (preg_match($patterns["page"], $key)) {
                    $class->setPage($array_values[0]);
                }
                //LIMIT
                else if (preg_match($patterns["limit"], $key)) {
                    $class->setLimit($array_values[0]);
                }
                //INCLUDE
                else {
                    $class->filter_include([
                        "key" => $key,
                        "array_values" => $array_values
                    ]);
                }
            }
            echo json_encode($class->getData());
        }
    } else if ($method === "POST") {
        $class = new PostController($ruta_solicitada);
        $json = json_decode(file_get_contents('php://input'), true);
        echo json_encode($class->setJson($json));
    } else if ($method === "PUT" || $method === "PATCH") {
        $class = new PutController($ruta_solicitada);
        $json = json_decode(file_get_contents('php://input'), true);
        $class->setJson($json);
        echo json_encode($class->put($id));
    } else if ($method === "DELETE") {
        $class = new DeleteController($ruta_solicitada);
        echo json_encode($class->delete($id));
    }
} else {
    echo "ruta no permitida";
}
