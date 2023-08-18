<?php


function generateCsv()
{
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename=usuarios.csv');


    $pdo = new PDO(
        'mysql:' .
            'host=' . MYSQL_SERVER . ';' .
            'dbname=' . MYSQL_DATABASE . ';' .
            'charset=' . MYSQL_CHARSET,
        MYSQL_USER,
        MYSQL_PASS,
        array(PDO::ATTR_PERSISTENT => true)
    );
    $stmt = $pdo->prepare('SELECT * FROM usuarios');
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $path = '/home/natanael/Downloads/';

    $out = fopen( 'php://output', 'w' );

    foreach ($results as $result) {
        fputcsv($out, $result, ",");
    }

    fclose($out);

}


