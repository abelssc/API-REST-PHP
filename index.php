<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

require $_SERVER["DOCUMENT_ROOT"]."/API-REST-PHP/config/dirs.php";

/*--===============================================
EN ESTE PRIMER BLOQUE OBTENDREMOS LOS DATOS DE LA PETICION:
**LA TABLA
**EL ID
**Y EL METODO DE LA PETICION 
=================================================*/

function obtenerDatosdelaURL(){
##Obtenemos la url /chatApp/chats/option
$url_actual = parse_url($_SERVER["REQUEST_URI"]);

##Eliminamos las / al inicio y al final de la url
$ruta = trim($url_actual["path"], '/');

##Obtenemos los fragmentos de la ruta
$fragmentos_de_ruta = explode("/", $ruta);
$tabla = $fragmentos_de_ruta[1];
$id = $fragmentos_de_ruta[2] ?? null;

##obtenemos el metodo
$method = $_SERVER["REQUEST_METHOD"];

return array(
    "url"=>$url_actual,
    "tabla"=>$tabla,
    "id"=>$id,
    "method"=>$method
);
}
$datosURL=obtenerDatosdelaURL();
/*--===============================================
EN EL RESTO DEL CODIGO:
**LLAMAREMOS AL CONTROLADOR
**EVALUAREMOS EL METODO DE ENVIO
**OBTENDREMOS EL JSON DE LA PETICION
**LLAMAREMOS A LA CLASE CORRESPONDIENTE
**IMPRIMIREMOS LA RESPUESTA EN JSON
=================================================*/
##INCLUIMOS EL CONTROLADOR PARA EL METODO ESPECIFICO
include(CONTROLLERS_PATH . $datosURL['method'] . '.php');

##EVALUAMOS EL METHODO DE ENVIO

    if ($datosURL['method'] === "GET") {
        //Obtenemos los valores de la ruta query
        //Hacemos esto porque la ruta query puede repetir nombres de clave, y la superglobal $_GET sobreescribe si hay algun duplicado
        //?ESTE ES EL CODIGO MAS DIFICIL DE LEER
        //?Se deberia mejorar
        //url_actual[query]=col1=val1&col2=val2&col3=val3....
        if (isset($datosURL["url"]["query"])) {
            $_GET = explode("&", $datosURL["url"]["query"]);
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
        $class = new GetController($datosURL['tabla']);

        ##PETICIONES CON /RUTA[/ID]?
        if (empty($_GET)) {
            echo (is_null($datosURL['id']))
                ? json_encode($class->getAll())
                : json_encode($class->getOne($datosURL['id']));
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
                    $key = preg_replace("/_ne$/","",$key);
                    $class->filter_exclude([
                        "key" => $key,
                        "array_values" => $array_values
                    ]);
                }
                //LIKE
                else if (preg_match($patterns["filter_like"], $key)) {
                    $key = preg_replace("/_like$/","",$key);
                    $class->filter_like([
                        "key" => $key,
                        "array_values" => $array_values
                    ]);
                }
                //GREATER THAN EQUAL
                else if (preg_match($patterns["filter_gte"], $key)) {
                    $key = preg_replace("/_gte$/","",$key);
                    $class->filter_gte([
                        "key" => $key,
                        "array_values" => $array_values
                    ]);
                }
                //LOW THAN EQUAL
                else if (preg_match($patterns["filter_lte"], $key)) {
                    $key = preg_replace("/_lte$/","",$key);
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
    } else if ($datosURL['method'] === "POST") {
        $class = new PostController($datosURL['tabla']);
        $json = json_decode(file_get_contents('php://input'), true);
        echo json_encode($class->setJson($json));
    } else if ($datosURL['method'] === "PUT" || $datosURL['method'] === "PATCH") {
        $class = new PutController($datosURL['tabla']);
        $json = json_decode(file_get_contents('php://input'), true);
        $class->setJson($json);
        echo json_encode($class->put($datosURL['id']));
    } else if ($datosURL['method'] === "DELETE") {
        $class = new DeleteController($datosURL['tabla']);
        echo json_encode($class->delete($datosURL['id']));
    }
