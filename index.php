<?php

use Core\Auth;
use Core\Routes;

session_start();
require_once __DIR__ . '/vendor/autoload.php';

require_once('./config.php');
require './core/Routes.php';
require '/var/www/web-store-php/core/Auth.php';

// --> habilitando o cors

if (isset($_SERVER["HTTP_ORIGIN"])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

header('Content-Type: application/json');

// <--


// rotas
$routes = new Routes();

$routes->post("/cadastro", "controllers/user/user.php:register");
$routes->get("/getAll", "controllers/user/user.php:getAll");

// middleware de autenticação
$routes->use(new Auth());


$routes->run();
