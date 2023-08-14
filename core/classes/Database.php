<?php

namespace core\classes;

use Exception;
use PDO;
use PDOException;

class Database
{

    private $conexao;

    private function conectar()
    {
        $this->conexao = new PDO(
            'mysql:' .
                'host=' . MYSQL_SERVER . ';' .
                'dbname=' . MYSQL_DATABASE . ';' .
                'charset=' . MYSQL_CHARSET,
            MYSQL_USER,
            MYSQL_PASS,
            array(PDO::ATTR_PERSISTENT => true)
        );

        $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    private function desconectar()
    {
        $this->conexao = null;
    }


    public function select($sql, $parametros = null)
    {
        if (!preg_match("/^SELECT/i", $sql)) {
            throw new Exception('Base de dados não é uma instrução select');
        }

        $this->conectar();

        $resultados = null;

        try {
            if (!empty($parametros)) {
                $executar = $this->conexao->prepare($sql);
                $executar->execute($parametros);
                $resultados = $executar->fetchAll(PDO::FETCH_CLASS);
            } else {
                $executar = $this->conexao->prepare($sql);
                $executar->execute();
                $resultados = $executar->fetchAll(PDO::FETCH_CLASS);
            }
        } catch (PDOException $e) {
            return false;
        }

        $this->desconectar();

        return $resultados;
    }


    public function insert($sql, $parametros = null)
    {
        if (!preg_match("/^INSERT/i", $sql)) {
            throw new Exception('Base de dados não é uma instrução INSERT');
        }

        $this->conectar();

        try {
            if (!empty($parametros)) {
                $executar = $this->conexao->prepare($sql);
                $executar->execute($parametros);
            } else {
                $executar = $this->conexao->prepare($sql);
                $executar->execute();
            }
        } catch (PDOException $e) {
            return array(
                "status" => 500,
                "msg" => $e->getMessage()
            );
        }

        $this->desconectar();


        return array(
            "status" => 200,
            "msg" => "Dados inseridos no banco"
        );
    }

    public function update($sql, $parametros = null)
    {
        if (!preg_match("/^UPDATE/i", $sql)) {
            throw new Exception('Base de dados não é uma instrução UPDATE');
        }

        $this->conectar();

        try {
            if (!empty($parametros)) {
                $executar = $this->conexao->prepare($sql);
                $executar->execute($parametros);
            } else {
                $executar = $this->conexao->prepare($sql);
                $executar->execute();
            }
        } catch (PDOException $e) {
            return array(
                "status" => 500,
                "msg" => $e->getMessage()
            );
        }

        $this->desconectar();


        return array(
            "status" => 200,
            "msg" => "Dados atualizados no banco"
        );
    }

    public function delete($sql, $parametros = null)
    {
        if (!preg_match("/^DELETE/i", $sql)) {
            throw new Exception('Base de dados não é uma instrução DELETE');
        }

        $this->conectar();

        try {
            if (!empty($parametros)) {
                $executar = $this->conexao->prepare($sql);
                $executar->execute($parametros);
            } else {
                $executar = $this->conexao->prepare($sql);
                $executar->execute();
            }
        } catch (PDOException $e) {
            return array(
                "status" => 500,
                "msg" => $e->getMessage()
            );
        }

        $this->desconectar();


        return array(
            "status" => 200,
            "msg" => "Dados apagados no banco"
        );
    }

    public function statement($sql, $parametros = null)
    {
        if (preg_match("/^(SELECT|INSERT|UPDATE|DELETE)/i", $sql)) {
            throw new Exception('Base de dados - instrução inválida');
        }

        $this->conectar();

        try {
            if (!empty($parametros)) {
                $executar = $this->conexao->prepare($sql);
                $executar->execute($parametros);
            } else {
                $executar = $this->conexao->prepare($sql);
                $executar->execute();
            }
        } catch (PDOException $e) {
            return array(
                "status" => 500,
                "msg" => $e->getMessage()
            );
        }

        $this->desconectar();


        return array(
            "status" => 200,
            "msg" => "Dados apagados no banco"
        );
    }

}
