<?php

use core\classes\Database;
use core\classes\Functions;
use Firebase\JWT\JWT;

function generateToken($email)
{
    $payload = [
        "id" => $email,
        "code" => $email,
    ];

    return $token = JWT::encode($payload, SECRET, 'HS256');
}

function getAll($request, $response)
{
    try {
        $db = new Database();
        $usuarios = $db->select("SELECT * FROM usuarios");

        return $response->json([
            "status" => 200,
            "data" => $usuarios,
        ]);
    } catch (Exception $e) {
        return $response->json([
            "status" => 500,
            "msg" => $e->getMessage(),
        ]);
    }
};

function register($request, $response)
{
    try {
        $db = new Database();
        $functions = new Functions();

        $data = [
            "name" => $request->body->name,
            "email" => $request->body->email,
            "password" => $request->body->password
        ];

        $email = $data["email"];


        if (!$functions->VerifyEmail($email)) {

            return $response->json([
                "status" => false,
                "error" => "Email inválido"
            ], 400);
        }

        // verificando se usuário já existe
        $userDb = $db->select("SELECT * from usuarios WHERE email = '$email'");

        if (count($userDb) === 1) {
            return $response->json([
                "status" => false,
                "error" => "Usuário já existe"
            ], 400);
        }

        $parametros = [
            ":nome" => $data["name"],
            ":email" => $data["email"],
            ":senha" => password_hash($data["password"], PASSWORD_DEFAULT)
        ];

        // salvando usuário
        $db->insert(
            "INSERT INTO usuarios (nome,email, senha)
             VALUES (:nome,:email, :senha)",
            $parametros
        );

        return $response->json([
            "status" => true,
            "msg" => "Usuário cadastrado com sucesso"
        ], 200);
    } catch (\Throwable $th) {
        return $response->json([
            "status" => false,
            "error" => $th->getMessage()
        ], 500);
    }
}

function login($request, $response)
{
    try {
        $db = new Database();

        $data = [
            "email" => $request->body->email,
            "password" => $request->body->password
        ];

        $parametros = [
            ":email" => $data["email"],
        ];

        $checkUser = $db->select("SELECT * FROM usuarios WHERE email = :email", $parametros);

        if (!$checkUser) {
            return $response->json([
                "status" => false,
                "error" => "Usuário não encontrado"
            ], 401);
        }

        $hashedPassword = $checkUser[0]->senha;

        if (password_verify($data['password'], $hashedPassword)) {
            //gerando token
            $token = generateToken($data['email']);

            return $response->json([
                "status" => true,
                "message" => "Login bem-sucedido",
                "token" => $token
            ], 200);

        } else {
            return $response->json([
                "status" => false,
                "error" => "Senha incorreta"
            ], 401);
        }
    } catch (\Throwable $th) {
        return $response->json([
            "status" => false,
            "error" => $th->getMessage()
        ], 500);
    }
}
