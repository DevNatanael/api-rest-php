<?php

use core\classes\Database;

function getAll($request, $response)
{
    try {
        $db = new Database();
        $clientes = $db->select("SELECT * FROM clientes");

        return $response->json([
            "status" => 200,
            "data" => $clientes,
        ]);
    } catch (Exception $e) {
        return $response->json([
            "status" => 500,
            "msg" => $e->getMessage(),
        ]);
    }
};

function postTest($request, $response){
    echo "post teste";
}
