<?php
$tries = 10;
do {
    try {
        $pdo = new PDO("mysql:host=db;dbname=testdb", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        break;
    } catch (PDOException $e) {
        echo "En attente de MySQL...<br>";
        sleep(2);
    }
} while (--$tries);
?>