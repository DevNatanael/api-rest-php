<?php

use core\classes\Database;

session_start();
require_once('./config.php');
require_once('./vendor/autoload.php');


$bd = new Database();

$clientes = $bd->select("SELECT * FROM clientes");
echo '<pre>';
print_r($clientes);


?>
