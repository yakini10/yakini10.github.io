<?php

session_start();
header('Content-Type: text/html; charset=UTF-8');

require_once 'config.php';

$db = getDB();
$messages = [];
$errors = [];
$values = [];

// ВЫХОД
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// ОБРАБОТКА ВХОДА
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['auth'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $userData = $stmt->fetch();
    
    if ($userData && password_verify($_POST['password'], $userData['password_hash'])) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['application_id'] = $userData['application_id'];
        header('Location: index.php');
        exit();
    } else {
        $messages[] = "<div class='error'>Неверные идентификационные данные</div>";
        include('login.php');
        exit();
    }
}

// ОТОБРАЖЕНИЕ GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    // Сообщение об успехе
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = '<div class="success">Спасибо, ваши данные сохранены.</div>';
    }
    
    // Отображение сгенерированных идентификаторов (первое подключение)
    if (!empty($_COOKIE['login']) && empty($_SESSION['user_id'])) {
        $messages[] = "<div class='success'>
            Ваши идентификационные данные для входа:<br>
            <strong>Логин :</strong> " . htmlspecialchars($_COOKIE['login']) . "<br>
            <strong>Пароль :</strong> " . htmlspecialchars($_COOKIE['password']) . "<br>
            <em>Сохраните их для последующих входов.</em>
        </div>";
        
        setcookie('login', '', time() - 3600);
        setcookie('password', '', time() - 3600);
    }
    
    // Получение ошибок из cookies
    $error_fields = ['fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'biography', 'contract_accepted'];
    
    foreach ($error_fields as $field) {
        if (!empty($_COOKIE[$field . '_error'])) {
            $errors[$field] = true;
            $messages[] = '<div class="error">' . htmlspecialchars($_COOKIE[$field . '_error_msg']) . '</div>';
            setcookie($field . '_error', '', time() - 3600);
            setcookie($field . '_error_msg', '', time() - 3600);
        }
    }
    
    // Получение введённых значений
    $values['fio'] = $_COOKIE['fio_value'] ?? '';
    $values['phone'] = $_COOKIE['phone_value'] ?? '';
    $values['email'] = $_COOKIE['email_value'] ?? '';
    $values['birth_date'] = $_COOKIE['birth_date_value'] ?? '';
    $values['gender'] = $_COOKIE['gender_value'] ?? '';
    $values['biography'] = $_COOKIE['biography_value'] ?? '';
    $values['contract_accepted'] = ($_COOKIE['contract_accepted_value'] ?? '') === 'yes';
    $values['languages'] = !empty($_COOKIE['languages_value']) 
        ? explode(',', $_COOKIE['languages_value']) 
        : [];
    
    // Если пользователь авторизован, приоритет у данных из БД
    if (!empty($_SESSION['application_id'])) {
        $stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
        $stmt->execute([$_SESSION['application_id']]);
        $dbValues = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dbValues) {
            $values = array_merge($values, $dbValues);
        }
        
        $stmt = $db->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
        $stmt->execute([$_SESSION['application_id']]);
        $values['languages'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    include('form.php');
    exit();
}

// ОБРАБОТКА POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['auth'])) {
    
    $has_errors = false;
    
    // Функция для сохранения ошибок в cookie
    function setError($field, $msg) {
        setcookie($field . '_error', '1', time() + 3600);
        setcookie($field . '_error_msg', $msg, time() + 3600);
    }
    
    // Валидация ФИО
    if (empty($_POST['fio'])) {
        $has_errors = true;
        setError('fio', 'Пожалуйста, введите ваше полное имя');
    }
    setcookie('fio_value', $_POST['fio'] ?? '', time() + 365 * 24 * 3600);
    
    // Валидация Телефона
    if (empty($_POST['phone'])) {
        $has_errors = true;
        setError('phone', 'Пожалуйста, введите ваш номер телефона');
    }
    setcookie('phone_value', $_POST['phone'] ?? '', time() + 365 * 24 * 3600);
    
    // Валидация Email
    if (empty($_POST['email'])) {
        $has_errors = true;
        setError('email', 'Пожалуйста, введите ваш адрес email');
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $has_errors = true;
        setError('email', 'Пожалуйста, введите действительный адрес email');
    }
    setcookie('email_value', $_POST['email'] ?? '', time() + 365 * 24 * 3600);
    
    // Валидация Даты рождения
    if (empty($_POST['birth_date'])) {
        $has_errors = true;
        setError('birth_date', 'Пожалуйста, введите вашу дату рождения');
    }
    setcookie('birth_date_value', $_POST['birth_date'] ?? '', time() + 365 * 24 * 3600);
    
    // Валидация Полa
    if (empty($_POST['gender'])) {
        $has_errors = true;
        setError('gender', 'Пожалуйста, выберите ваш пол');
    }
    setcookie('gender_value', $_POST['gender'] ?? '', time() + 365 * 24 * 3600);
    
    // Валидация Языков
    if (empty($_POST['languages'])) {
        $has_errors = true;
        setError('languages', 'Пожалуйста, выберите хотя бы один язык программирования');
    }
    setcookie('languages_value', implode(',', $_POST['languages'] ?? []), time() + 365 * 24 * 3600);
    
    // Валидация Биографии
    if (empty($_POST['biography'])) {
        $has_errors = true;
        setError('biography', 'Пожалуйста, введите вашу биографию');
    }
    setcookie('biography_value', $_POST['biography'] ?? '', time() + 365 * 24 * 3600);
    
    // Валидация Договора
    if (empty($_POST['contract_accepted'])) {
        $has_errors = true;
        setError('contract_accepted', 'Пожалуйста, примите условия договора');
    }
    setcookie('contract_accepted_value', isset($_POST['contract_accepted']) ? 'yes' : '', time() + 365 * 24 * 3600);
    
    if ($has_errors) {
        header('Location: index.php');
        exit();
    }
    
    // Удаление ошибок
    $fields = ['fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'biography', 'contract_accepted'];
    foreach ($fields as $f) {
        setcookie($f . '_error', '', time() - 3600);
        setcookie($f . '_error_msg', '', time() - 3600);
    }
    
    // СОХРАНЕНИЕ В БД
    $db->beginTransaction();
    
    if (!empty($_SESSION['application_id'])) {
        // ОБНОВЛЕНИЕ
        $stmt = $db->prepare("UPDATE application SET 
            fio = ?, phone = ?, email = ?, birth_date = ?, 
            gender = ?, biography = ?, contract_accepted = ? 
            WHERE id = ?");
        $stmt->execute([
            $_POST['fio'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['birth_date'],
            $_POST['gender'],
            $_POST['biography'],
            1,
            $_SESSION['application_id']
        ]);
        
        // Обновление языков
        $stmt = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
        $stmt->execute([$_SESSION['application_id']]);
        
        foreach ($_POST['languages'] as $lang) {
            $stmt = $db->prepare("INSERT INTO application_languages VALUES (?, ?)");
            $stmt->execute([$_SESSION['application_id'], $lang]);
        }
        
    } else {
        // НОВАЯ РЕГИСТРАЦИЯ
        $stmt = $db->prepare("INSERT INTO application (fio, phone, email, birth_date, gender, biography, contract_accepted) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['fio'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['birth_date'],
            $_POST['gender'],
            $_POST['biography'],
            1
        ]);
        
        $app_id = $db->lastInsertId();
        
        // Сохранение языков
        foreach ($_POST['languages'] as $lang) {
            $stmt = $db->prepare("INSERT INTO application_languages VALUES (?, ?)");
            $stmt->execute([$app_id, $lang]);
        }
        
        // Генерация идентификаторов
        $login = 'user' . rand(1000, 9999);
        $password = bin2hex(random_bytes(4));
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO users (login, password_hash, application_id) VALUES (?, ?, ?)");
        $stmt->execute([$login, $hash, $app_id]);
        
        setcookie('login', $login, time() + 3600);
        setcookie('password', $password, time() + 3600);
    }
    
    $db->commit();
    
    setcookie('save', '1', time() + 3600);
    header('Location: index.php');
    exit();
}
?>
