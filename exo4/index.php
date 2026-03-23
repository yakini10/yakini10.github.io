<?php
/**
 * Formulaire d'application avec validation côté serveur
 * Utilisation de cookies pour la persistance des données
 */

header('Content-Type: text/html; charset=UTF-8');

// Fonction de validation avec expressions régulières
function validateFIO($fio) {
    // Lettres, espaces, tirets, apostrophes (cyrillique et latin)
    if (empty($fio)) return 'Le nom est obligatoire.';
    if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-\']+$/u', $fio)) {
        return 'Le nom ne doit contenir que des lettres, espaces, tirets et apostrophes.';
    }
    return null;
}

function validatePhone($phone) {
    if (empty($phone)) return 'Le téléphone est obligatoire.';
    // Format: +7XXXXXXXXXX, 8XXXXXXXXXX, ou XXXXXXXXXX (10-15 chiffres)
    if (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,20}$/', $phone)) {
        return 'Format de téléphone invalide. Utilisez uniquement des chiffres, espaces, tirets et parenthèses.';
    }
    return null;
}

function validateEmail($email) {
    if (empty($email)) return 'L\'email est obligatoire.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Format d\'email invalide (exemple: nom@domaine.com).';
    }
    return null;
}

function validateBirthDate($birth_date) {
    if (empty($birth_date)) return 'La date de naissance est obligatoire.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_date)) {
        return 'Format de date invalide (AAAA-MM-JJ).';
    }
    $date = DateTime::createFromFormat('Y-m-d', $birth_date);
    if (!$date || $date->format('Y-m-d') !== $birth_date) {
        return 'Date invalide.';
    }
    $min_date = new DateTime('1900-01-01');
    $max_date = new DateTime('today');
    if ($date < $min_date || $date > $max_date) {
        return 'Date de naissance invalide (entre 1900 et aujourd\'hui).';
    }
    return null;
}

function validateGender($gender) {
    if (empty($gender)) return 'Le sexe est obligatoire.';
    if (!in_array($gender, ['male', 'female'])) {
        return 'Sexe invalide.';
    }
    return null;
}

function validateLanguages($languages) {
    if (empty($languages) || !is_array($languages)) {
        return 'Sélectionnez au moins un langage de programmation.';
    }
    $valid_langs = range(1, 12);
    foreach ($languages as $lang) {
        if (!in_array((int)$lang, $valid_langs)) {
            return 'Langage de programmation invalide.';
        }
    }
    return null;
}

function validateBiography($biography) {
    if (empty($biography)) return 'La biographie est obligatoire.';
    // 3-1000 caractères, caractères de base autorisés
    if (strlen($biography) < 3 || strlen($biography) > 1000) {
        return 'La biographie doit contenir entre 3 et 1000 caractères.';
    }
    if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ0-9\s\-\.,!?\'"\(\):;]+$/u', $biography)) {
        return 'La biographie contient des caractères non autorisés.';
    }
    return null;
}

function validateContract($contract) {
    if (empty($contract)) return 'Vous devez accepter le contrat.';
    return null;
}

// Gestion des cookies pour les valeurs par défaut
function getCookieValue($name, $default = '') {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = [];
    $errors = [];
    $values = [];

    // Récupérer les messages de succès
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = '<div class="success">Merci, les résultats ont été sauvegardés.</div>';
    }

    // Récupérer les erreurs des cookies
    $error_fields = ['fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'biography', 'contract_accepted'];
    foreach ($error_fields as $field) {
        if (!empty($_COOKIE[$field . '_error'])) {
            $errors[$field] = true;
            setcookie($field . '_error', '', time() - 3600);
            $messages[] = '<div class="error">' . htmlspecialchars($_COOKIE[$field . '_error_msg']) . '</div>';
            setcookie($field . '_error_msg', '', time() - 3600);
        }
    }

    // Récupérer les valeurs sauvegardées
    $values['fio'] = getCookieValue('fio_value');
    $values['phone'] = getCookieValue('phone_value');
    $values['email'] = getCookieValue('email_value');
    $values['birth_date'] = getCookieValue('birth_date_value');
    $values['gender'] = getCookieValue('gender_value');
    $values['biography'] = getCookieValue('biography_value');
    $values['contract_accepted'] = getCookieValue('contract_accepted_value') == 'yes';
    
    // Pour les langues (multiple)
    $saved_languages = getCookieValue('languages_value');
    $values['languages'] = !empty($saved_languages) ? explode(',', $saved_languages) : [];

    // Inclure le formulaire
    include('form.php');
    exit();
} 
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $has_errors = false;
    $error_messages = [];

    // Valider chaque champ
    $fio_error = validateFIO($_POST['fio'] ?? '');
    if ($fio_error) {
        $has_errors = true;
        $error_messages['fio'] = $fio_error;
        setcookie('fio_error', '1', time() + 3600);
        setcookie('fio_error_msg', $fio_error, time() + 3600);
    }
    setcookie('fio_value', $_POST['fio'] ?? '', time() + 365 * 24 * 3600);

    $phone_error = validatePhone($_POST['phone'] ?? '');
    if ($phone_error) {
        $has_errors = true;
        $error_messages['phone'] = $phone_error;
        setcookie('phone_error', '1', time() + 3600);
        setcookie('phone_error_msg', $phone_error, time() + 3600);
    }
    setcookie('phone_value', $_POST['phone'] ?? '', time() + 365 * 24 * 3600);

    $email_error = validateEmail($_POST['email'] ?? '');
    if ($email_error) {
        $has_errors = true;
        $error_messages['email'] = $email_error;
        setcookie('email_error', '1', time() + 3600);
        setcookie('email_error_msg', $email_error, time() + 3600);
    }
    setcookie('email_value', $_POST['email'] ?? '', time() + 365 * 24 * 3600);

    $birth_date_error = validateBirthDate($_POST['birth_date'] ?? '');
    if ($birth_date_error) {
        $has_errors = true;
        $error_messages['birth_date'] = $birth_date_error;
        setcookie('birth_date_error', '1', time() + 3600);
        setcookie('birth_date_error_msg', $birth_date_error, time() + 3600);
    }
    setcookie('birth_date_value', $_POST['birth_date'] ?? '', time() + 365 * 24 * 3600);

    $gender_error = validateGender($_POST['gender'] ?? '');
    if ($gender_error) {
        $has_errors = true;
        $error_messages['gender'] = $gender_error;
        setcookie('gender_error', '1', time() + 3600);
        setcookie('gender_error_msg', $gender_error, time() + 3600);
    }
    setcookie('gender_value', $_POST['gender'] ?? '', time() + 365 * 24 * 3600);

    $languages_error = validateLanguages($_POST['languages'] ?? []);
    if ($languages_error) {
        $has_errors = true;
        $error_messages['languages'] = $languages_error;
        setcookie('languages_error', '1', time() + 3600);
        setcookie('languages_error_msg', $languages_error, time() + 3600);
    }
    if (!empty($_POST['languages'])) {
        setcookie('languages_value', implode(',', $_POST['languages']), time() + 365 * 24 * 3600);
    }

    $biography_error = validateBiography($_POST['biography'] ?? '');
    if ($biography_error) {
        $has_errors = true;
        $error_messages['biography'] = $biography_error;
        setcookie('biography_error', '1', time() + 3600);
        setcookie('biography_error_msg', $biography_error, time() + 3600);
    }
    setcookie('biography_value', $_POST['biography'] ?? '', time() + 365 * 24 * 3600);

    $contract_error = validateContract($_POST['contract_accepted'] ?? '');
    if ($contract_error) {
        $has_errors = true;
        $error_messages['contract_accepted'] = $contract_error;
        setcookie('contract_accepted_error', '1', time() + 3600);
        setcookie('contract_accepted_error_msg', $contract_error, time() + 3600);
    }
    setcookie('contract_accepted_value', isset($_POST['contract_accepted']) ? 'yes' : '', time() + 365 * 24 * 3600);

    if ($has_errors) {
        header('Location: index.php');
        exit();
    }

    // Supprimer tous les cookies d'erreur
    $error_fields = ['fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'biography', 'contract_accepted'];
    foreach ($error_fields as $field) {
        setcookie($field . '_error', '', time() - 3600);
        setcookie($field . '_error_msg', '', time() - 3600);
    }

    // Connexion à la base de données
    $user = 'u82383';
    $pass = 'dt54#FDrt';
    try {
        $db = new PDO('mysql:host=localhost;dbname=u82383', $user, $pass,
            [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $db->beginTransaction();

        $stmt = $db->prepare("INSERT INTO application (fio, phone, email, birth_date, gender, biography, contract_accepted) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['fio'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['birth_date'],
            $_POST['gender'],
            $_POST['biography'],
            1
        ]);

        $application_id = $db->lastInsertId();

        $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
        foreach ($_POST['languages'] as $lang_id) {
            $stmt->execute([$application_id, $lang_id]);
        }

        $db->commit();

        setcookie('save', '1', time() + 3600);
        header('Location: index.php');
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        error_log('Database error: ' . $e->getMessage());
        $messages[] = '<div class="error">Une erreur est survenue lors de l\'enregistrement.</div>';
        include('form.php');
        exit();
    }
}
