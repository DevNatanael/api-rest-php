<?php

use Core\Routes;

session_start();
require_once __DIR__ . '/vendor/autoload.php';

require_once('./config.php');
require './core/Routes.php';


// rotas
$routes = new Routes();
$routes->get("/test",  "controllers/clientes.php:getAll");
//$routes->post("/", "controllers/clientes.php:postTest");


$routes->run();
