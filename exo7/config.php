<?php

// Configuration DB

define('DB_HOST', 'localhost');
define('DB_NAME', 'u82383');
define('DB_USER', 'u82383');
define('DB_PASS', 'CHANGE_ME');

function getDB() {
    static $db = null;

    if ($db === null) {
        try {
            $db = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die('Database connection error');
        }
    }

    return $db;
}

function getLanguagesList() {
    return [
        1 => 'Pascal',
        2 => 'C',
        3 => 'C++',
        4 => 'JavaScript',
        5 => 'PHP',
        6 => 'Python',
        7 => 'Java',
        8 => 'Haskell',
        9 => 'Clojure',
        10 => 'Prolog',
        11 => 'Scala',
        12 => 'Go'
    ];
}
?>
