<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
  if (!empty($_GET['save'])) {
    // Если есть параметр save, то выводим сообщение пользователю.
    print('Спасибо, результаты сохранены.');
  }
  // Включаем содержимое файла form.php.
  include('form.php');
  // Завершаем работу скрипта.
  exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в БД.

// Проверяем ошибки.
$errors = [];

if (empty($_POST['fio'])) {
  $errors[] = 'Заполните имя.';
}

if (empty($_POST['phone'])) {
  $errors[] = 'Заполните телефон.';
}

if (empty($_POST['email'])) {
  $errors[] = 'Заполните email.';
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  $errors[] = 'Некорректный email.';
}

if (empty($_POST['birth_date'])) {
  $errors[] = 'Заполните дату рождения.';
}

if (empty($_POST['gender'])) {
  $errors[] = 'Выберите пол.';
}

if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
  $errors[] = 'Выберите хотя бы один язык программирования.';
}

if (empty($_POST['biography'])) {
  $errors[] = 'Заполните биографию.';
}

if (empty($_POST['contract_accepted'])) {
  $errors[] = 'Необходимо подтверждение для заключения контракта.';
}

// S'il y a des erreurs, on les affiche ET on inclut le formulaire
if (!empty($errors)) {
  foreach ($errors as $error) {
    print($error . "<br>");
  }
  include('form.php');
  exit();
} else {
  // *************
  // Тут необходимо проверить правильность заполнения всех остальных полей.
  // *************

  // Сохранение в базу данных.
  $user = 'u82383'; // Заменить на ваш логин uXXXXX
  $pass = 'dt54#FDrt'; // Заменить на пароль
  $db = new PDO('mysql:host=localhost;dbname=u82383', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX

  // Подготовленный запрос. Не именованные метки.
  try {
    $stmt = $db->prepare("INSERT INTO application (fio, phone, email, birth_date, gender, biography, contract_accepted) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $_POST['fio'], 
      $_POST['phone'],
      $_POST['email'],
      $_POST['birth_date'],
      $_POST['gender'],
      $_POST['biography'],
      isset($_POST['contract_accepted']) ? 1 : 0
    ]);
    
    $application_id = $db->lastInsertId();
    $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
    
    foreach ($_POST['languages'] as $lang_id) {
      $stmt->execute([$application_id, $lang_id]);
    }
    
    header('Location: ?save=1');
    exit();
    
  } catch (PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
  }
}
?>
