<?php
session_start();
require_once 'config.php';

$db = getDB();

// ABLE ADMIN + ADMIN PAR DÉFAUT
$db->exec("
    CREATE TABLE IF NOT EXISTS admin (
        id INT PRIMARY KEY AUTO_INCREMENT,
        login VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL
    )
");

// Création admin si vide
$stmt = $db->query("SELECT COUNT(*) FROM admin");
if ($stmt->fetchColumn() == 0) {
    $hash = password_hash('123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO admin (login, password_hash) VALUES (?, ?)");
    $stmt->execute(['admin', $hash]);
}

// HTTP AUTH 
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    exit('Требуется авторизация');
}

$stmt = $db->prepare("SELECT * FROM admin WHERE login = ?");
$stmt->execute([$_SERVER['PHP_AUTH_USER']]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($_SERVER['PHP_AUTH_PW'], $admin['password_hash'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    exit('Неверные данные');
}

// ACTIONS
$action = $_GET['action'] ?? 'list';
$message = null;
$error = null;

// DELETE (MODIFIÉ avec CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['action'])
    && $_GET['action'] === 'delete'
    && isset($_POST['delete_id'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !verifyCSRFToken($_POST['csrf_token'])
    ) {

        $error = "CSRF token invalide";

    } else {

        $id = (int)$_POST['delete_id'];

        try {

            $db->beginTransaction();

            $db->prepare("
                DELETE FROM application_languages
                WHERE application_id = ?
            ")->execute([$id]);

            $db->prepare("
                DELETE FROM users
                WHERE application_id = ?
            ")->execute([$id]);

            $db->prepare("
                DELETE FROM application
                WHERE id = ?
            ")->execute([$id]);

            $db->commit();

            $message = "Данные удалены.";

        } catch (Exception $e) {

            $db->rollBack();

            $error = "Ошибка удаления.";
        }
    }
}
// EDIT
$edit_data = null;
$edit_languages = [];

if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // AJOUTÉ - Vérification CSRF
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            die("Erreur CSRF");
        }
        
        try {
            $db->beginTransaction();

            $db->prepare("
                UPDATE application SET 
                    fio = ?,
                    phone = ?,
                    email = ?,
                    birth_date = ?,
                    gender = ?,
                    biography = ?,
                    contract_accepted = ?
                WHERE id = ?
            ")->execute([
                $_POST['fio'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                $_POST['birth_date'] ?? null,
                $_POST['gender'] ?? null,
                $_POST['biography'] ?? null,
                isset($_POST['contract_accepted']) ? 1 : 0,
                $id
            ]);

            // LANGUAGES RESET
            $db->prepare("DELETE FROM application_languages WHERE application_id=?")->execute([$id]);

            if (!empty($_POST['languages']) && is_array($_POST['languages'])) {
                foreach ($_POST['languages'] as $lang) {
                    $db->prepare("
                        INSERT INTO application_languages (application_id, language_id)
                        VALUES (?, ?)
                    ")->execute([$id, $lang]);
                }
            }

            $db->commit();
            header('Location: admin.php');
            exit();

        } catch (Exception $e) {
            $db->rollBack();
            $error = "Ошибка изменения.";
        }
    }

    $stmt = $db->prepare("SELECT * FROM application WHERE id=?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();

    if ($edit_data) {
        $stmt = $db->prepare("SELECT language_id FROM application_languages WHERE application_id=?");
        $stmt->execute([$id]);
        $edit_languages = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $action = 'list';
        $error = "Не найдено.";
    }
}

// DATA
$applications = $db->query("
    SELECT a.*, u.login 
    FROM application a
    LEFT JOIN users u ON a.id = u.application_id
    ORDER BY a.id DESC
")->fetchAll();

// STATS
$statistiques = $db->query("
    SELECT l.name, COUNT(al.application_id) as nb
    FROM programming_languages l
    LEFT JOIN application_languages al ON l.id = al.language_id
    GROUP BY l.id
    ORDER BY nb DESC
")->fetchAll();

$langs_list = getLanguagesList();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Admin</title>
</head>
<body>

<h1>Администрирование</h1>

<?php if ($message) echo "<p>" . e($message) . "</p>"; ?>
<?php if ($error) echo "<p>" . e($error) . "</p>"; ?>

<h2>Статистика</h2>
<ul>
<?php foreach ($statistiques as $s): ?>
<li><?= e($s['name']) ?> : <?= (int)$s['nb'] ?></li>
<?php endforeach; ?>
</ul>

<?php if ($action === 'edit' && $edit_data): ?>

<h2>Редактирование</h2>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">  <!-- AJOUTÉ -->

    <input name="fio" value="<?= e($edit_data['fio'] ?? '') ?>" placeholder="ФИО">

    <input name="phone" value="<?= e($edit_data['phone'] ?? '') ?>" placeholder="Телефон">

    <input name="email" value="<?= e($edit_data['email'] ?? '') ?>" placeholder="Email">

    <br><br>

    <label>Дата рождения</label>
    <input type="date" name="birth_date"
           value="<?= e($edit_data['birth_date'] ?? '') ?>">

    <br><br>

    <label>Пол</label>
    <select name="gender">
        <option value="male" <?= ($edit_data['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Мужской</option>
        <option value="female" <?= ($edit_data['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Женский</option>
    </select>

    <br><br>

    <label>Биография</label><br>
    <textarea name="biography" rows="5"><?= e($edit_data['biography'] ?? '') ?></textarea>

    <br><br>

    <label>
        <input type="checkbox" name="contract_accepted"
            <?= !empty($edit_data['contract_accepted']) ? 'checked' : '' ?>>
       С контрактом ознакомлен(а)
    </label>

    <br><br>

    <button>Сохранить</button>
</form>

<?php else: ?>

<h2>Заявки</h2>
<table border="1">
<tr><th>ID</th><th>ФИО</th><th>Email</th><th>Действия</th></tr>

<?php foreach ($applications as $app): ?>
<tr>
    <td><?= e($app['id']) ?></td>
    <td><?= e($app['fio']) ?></td>
    <td><?= e($app['email']) ?></td>
    <td>
        <a href="?action=edit&id=<?= urlencode($app['id']) ?>&csrf_token=<?= urlencode(getCSRFToken()) ?>">Edit</a>
    </td>
    <td>
    <form method="POST"
      action="admin.php?action=delete"
      style="display:inline;"
      onsubmit="return confirm('Удалить запись #<?= (int)$app['id'] ?> ?');">

    <input type="hidden"
           name="delete_id"
           value="<?= (int)$app['id'] ?>">

    <input type="hidden"
           name="csrf_token"
           value="<?= e(getCSRFToken()) ?>">

    <button type="submit">
        Delete
    </button>
</form>
</td>
</tr>
<?php endforeach; ?>

</table>

<?php endif; ?>

</body>
</html>
