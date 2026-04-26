<?php


// Identifiants BDD
define('DB_HOST', 'localhost');
define('DB_NAME', 'u82383');
define('DB_USER', 'u82383');
define('DB_PASS', 'dt54#FDrt');

/**
 * Retourne une connexion PDO à la base de données
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
            die('Erreur de connexion à la base de données');
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


function validateFormData($data) {
    $errors = [];
    
    if (empty($data['fio'])) $errors['fio'] = 'Veuillez saisir votre nom complet';
    if (empty($data['phone'])) $errors['phone'] = 'Veuillez saisir votre téléphone';
    if (empty($data['email'])) $errors['email'] = 'Veuillez saisir votre email';
    if (empty($data['birth_date'])) $errors['birth_date'] = 'Veuillez saisir votre date de naissance';
    if (empty($data['gender'])) $errors['gender'] = 'Veuillez sélectionner votre sexe';
    if (empty($data['languages']) || count($data['languages']) == 0) $errors['languages'] = 'Veuillez sélectionner au moins un langage';
    if (empty($data['biography'])) $errors['biography'] = 'Veuillez saisir votre biographie';
    if (empty($data['contract_accepted'])) $errors['contract_accepted'] = 'Veuillez accepter le contrat';
    
    return $errors;
}
