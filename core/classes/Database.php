<?php

namespace core\classes;

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
        $this->conectar();

        $resultados = null;

        try {
            if(!empty($parametros)){
                $executar = $this->conexao->prepare($sql);
                $executar->execute($parametros);
                $resultados = $executar->fetchAll(PDO::FETCH_CLASS);
            }else{
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
}
