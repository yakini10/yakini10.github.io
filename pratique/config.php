<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u82383');
define('DB_USER', 'u82383');
define('DB_PASS', 'dt54#FDrt');


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

session_start();
?>
