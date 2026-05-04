<?php

// Данные для подключения к БД
define('DB_HOST', 'localhost');
define('DB_NAME', 'u82383');
define('DB_USER', 'u82383');
define('DB_PASS', 'dt54#FDrt');

/**
 * Возвращает PDO подключение к базе данных
 * @return PDO
 */
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $db = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die('Ошибка подключения к базе данных');
        }
    }
    
    return $db;
}

/**
 * Возвращает список языков программирования
 * @return array
 */
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

function validateFormData($data) {
    $errors = [];
    
    if (empty($data['fio'])) $errors['fio'] = 'Пожалуйста, введите ваше полное имя';
    if (empty($data['phone'])) $errors['phone'] = 'Пожалуйста, введите ваш телефон';
    if (empty($data['email'])) $errors['email'] = 'Пожалуйста, введите ваш email';
    if (empty($data['birth_date'])) $errors['birth_date'] = 'Пожалуйста, введите вашу дату рождения';
    if (empty($data['gender'])) $errors['gender'] = 'Пожалуйста, выберите ваш пол';
    if (empty($data['languages']) || count($data['languages']) == 0) $errors['languages'] = 'Пожалуйста, выберите хотя бы один язык';
    if (empty($data['biography'])) $errors['biography'] = 'Пожалуйста, введите вашу биографию';
    if (empty($data['contract_accepted'])) $errors['contract_accepted'] = 'Пожалуйста, примите условия договора';
    
    return $errors;
}
