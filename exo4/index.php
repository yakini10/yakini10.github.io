<?php
/**
 * Форма заявки с валидацией на стороне сервера
 * Использование cookies для сохранения данных
 */

header('Content-Type: text/html; charset=UTF-8');

// Функции валидации с регулярными выражениями
function validateFIO($fio) {
    // Буквы, пробелы, дефисы, апострофы (кириллица и латиница)
    if (empty($fio)) return 'Заполните имя.';
    if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-\']+$/u', $fio)) {
        return 'Имя должно содержать только буквы, пробелы, дефисы и апострофы.';
    }
    return null;
}

function validatePhone($phone) {
    if (empty($phone)) return 'Заполните телефон.';
    // Формат: +7XXXXXXXXXX, 8XXXXXXXXXX, или XXXXXXXXXX (10-15 цифр)
    if (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,20}$/', $phone)) {
        return 'Неверный формат телефона. Используйте только цифры, пробелы, дефисы и скобки.';
    }
    return null;
}

function validateEmail($email) {
    if (empty($email)) return 'Заполните email.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Неверный формат email (пример: name@domain.com).';
    }
    return null;
}

function validateBirthDate($birth_date) {
    if (empty($birth_date)) return 'Заполните дату рождения.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_date)) {
        return 'Неверный формат даты (ГГГГ-ММ-ДД).';
    }
    $date = DateTime::createFromFormat('Y-m-d', $birth_date);
    if (!$date || $date->format('Y-m-d') !== $birth_date) {
        return 'Неверная дата.';
    }
    $min_date = new DateTime('1900-01-01');
    $max_date = new DateTime('today');
    if ($date < $min_date || $date > $max_date) {
        return 'Неверная дата рождения (между 1900 и сегодняшним днем).';
    }
    return null;
}

function validateGender($gender) {
    if (empty($gender)) return 'Выберите пол.';
    if (!in_array($gender, ['male', 'female'])) {
        return 'Неверное значение пола.';
    }
    return null;
}

function validateLanguages($languages) {
    if (empty($languages) || !is_array($languages)) {
        return 'Выберите хотя бы один язык программирования.';
    }
    $valid_langs = range(1, 12);
    foreach ($languages as $lang) {
        if (!in_array((int)$lang, $valid_langs)) {
            return 'Неверный язык программирования.';
        }
    }
    return null;
}

function validateBiography($biography) {
    if (empty($biography)) return 'Заполните биографию.';
    // 3-1000 символов, разрешены основные символы
    if (strlen($biography) < 3 || strlen($biography) > 1000) {
        return 'Биография должна содержать от 3 до 1000 символов.';
    }
    if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ0-9\s\-\.,!?\'"\(\):;]+$/u', $biography)) {
        return 'Биография содержит недопустимые символы.';
    }
    return null;
}

function validateContract($contract) {
    if (empty($contract)) return 'Необходимо подтверждение для заключения контракта.';
    return null;
}

// Получение значения из cookie
function getCookieValue($name, $default = '') {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = [];
    $errors = [];
    $values = [];

    // Получаем сообщение об успешном сохранении
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = '<div class="success">Спасибо, результаты сохранены.</div>';
    }

    // Получаем ошибки из cookies
    $error_fields = ['fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'biography', 'contract_accepted'];
    foreach ($error_fields as $field) {
        if (!empty($_COOKIE[$field . '_error'])) {
            $errors[$field] = true;
            setcookie($field . '_error', '', time() - 3600);
            $messages[] = '<div class="error">' . htmlspecialchars($_COOKIE[$field . '_error_msg']) . '</div>';
            setcookie($field . '_error_msg', '', time() - 3600);
        }
    }

    // Получаем сохраненные значения
    $values['fio'] = getCookieValue('fio_value');
    $values['phone'] = getCookieValue('phone_value');
    $values['email'] = getCookieValue('email_value');
    $values['birth_date'] = getCookieValue('birth_date_value');
    $values['gender'] = getCookieValue('gender_value');
    $values['biography'] = getCookieValue('biography_value');
    $values['contract_accepted'] = getCookieValue('contract_accepted_value') == 'yes';
    
    // Для языков (множественный выбор)
    $saved_languages = getCookieValue('languages_value');
    $values['languages'] = !empty($saved_languages) ? explode(',', $saved_languages) : [];

    // Включаем форму
    include('form.php');
    exit();
} 
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $has_errors = false;
    $error_messages = [];

    // Валидация каждого поля
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

    // Удаляем все cookies с ошибками
    $error_fields = ['fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'biography', 'contract_accepted'];
    foreach ($error_fields as $field) {
        setcookie($field . '_error', '', time() - 3600);
        setcookie($field . '_error_msg', '', time() - 3600);
    }

    // Подключение к базе данных
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
        $messages[] = '<div class="error">Произошла ошибка при сохранении данных.</div>';
        include('form.php');
        exit();
    }
}
