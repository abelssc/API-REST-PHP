<?php
    class Conexion{
        const HOST="localhost";
        const USER="root";
        const PASSWORD="";
        const DATABASE="blueopticas";

        public static function getConexion(){
            try {
                $dsn= "mysql:host=".self::HOST.";dbname=".self::DATABASE;
                $dbh= new PDO($dsn,self::USER,self::PASSWORD);
                // return "conected";
                $dbh->exec("SET NAMES 'utf8'");
                return $dbh;
    
            } catch (PDOException $e) {
                echo "No se pudo Crear la Coneccion $e";
                exit;
            }
        }


    }
  
   
?>