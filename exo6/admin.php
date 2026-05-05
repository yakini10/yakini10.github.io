<?php
session_start();
require_once 'config.php';

$db = getDB();

//
// 1. TABLE ADMIN + ADMIN PAR DÉFAUT
//
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

//
// 2. HTTP AUTH (OBLIGATOIRE)
//
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

//
// 3. ACTIONS
//
$action = $_GET['action'] ?? 'list';
$message = null;
$error = null;

//
// DELETE
//
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        $db->beginTransaction();

        $db->prepare("DELETE FROM application_languages WHERE application_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM users WHERE application_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM application WHERE id = ?")->execute([$id]);

        $db->commit();
        $message = "Данные удалены.";
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Ошибка удаления.";
    }
}

//
// EDIT
//
$edit_data = null;
$edit_languages = [];

if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

//
// 4. DATA
//
$applications = $db->query("
    SELECT a.*, u.login 
    FROM application a
    LEFT JOIN users u ON a.id = u.application_id
    ORDER BY a.id DESC
")->fetchAll();

//
// 5. STATS
//
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

<?php if ($message) echo "<p>$message</p>"; ?>
<?php if ($error) echo "<p>$error</p>"; ?>

<h2>Статистика</h2>
<ul>
<?php foreach ($statistiques as $s): ?>
<li><?= htmlspecialchars($s['name']) ?> : <?= $s['nb'] ?></li>
<?php endforeach; ?>
</ul>

<?php if ($action === 'edit' && $edit_data): ?>

<h2>Редактирование</h2>
<form method="POST">
<input name="fio" value="<?= htmlspecialchars($edit_data['fio']) ?>">
<input name="phone" value="<?= htmlspecialchars($edit_data['phone']) ?>">
<input name="email" value="<?= htmlspecialchars($edit_data['email']) ?>">
<button>Сохранить</button>
</form>

<?php else: ?>

<h2>Заявки</h2>
<table border="1">
<tr><th>ID</th><th>ФИО</th><th>Email</th><th>Действия</th></tr>

<?php foreach ($applications as $app): ?>
<tr>
<td><?= $app['id'] ?></td>
<td><?= htmlspecialchars($app['fio']) ?></td>
<td><?= htmlspecialchars($app['email']) ?></td>
<td>
<a href="?action=edit&id=<?= $app['id'] ?>">Edit</a>
<a href="?action=delete&id=<?= $app['id'] ?>">Delete</a>
</td>
</tr>
<?php endforeach; ?>

</table>

<?php endif; ?>

</body>
</html>
