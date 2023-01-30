<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

$method=$_SERVER["REQUEST_METHOD"];

if($method==="GET"){
    include  __DIR__."/controllers/get.php";
}else if($method==="POST"){
    include __DIR__."/controllers/post.php";
}else if($method==="PUT"){
    include __DIR__."/controllers/put.php";
}else if($method==="DELETE"){
    include __DIR__."/controllers/delete.php";
}else{
    echo "Metodo no Permitido";
}