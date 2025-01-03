<?php
$dbname = 'php_pdo';
$userName = 'root';
$host = 'localhost';
$pdo_connection = null;
try{
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

    $pdo_connection = new PDO( $dsn, $userName ,NULL , 
                              array(
                                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                                )
                            );
}catch(PDOException $e){
     echo $e->getMessage();
}
