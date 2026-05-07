<?php
define('DB_HOST','localhost');
define('DB_NAME','u82383');
define('DB_USER','u82383');
define('DB_PASS','secret');

function getDB(){
    static $db=null;
    if($db===null){
        $db=new PDO(
            'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT]
        );
    }
    return $db;
}
?>
