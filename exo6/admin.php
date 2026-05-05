<?php

/**
 * Задача 6. Реализовать вход администратора с использованием
 * HTTP-авторизации для просмотра и удаления результатов.
 **/

require_once 'config.php';
$db = getDB();

// ============================================
// HTTP-AUTHENTIFICATION (methode du professeur)
// ============================================
if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    md5($_SERVER['PHP_AUTH_PW']) != md5('123')) {
    
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    print('<p>Логин: admin<br>Пароль: 123</p>');
    exit();
}

// Si on arrive ici, l'admin est authentifie
// ============================================

// Обработка действий (удаление)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        $db->beginTransaction();
        
        // Удаляем связи с языками
        $stmt = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
        $stmt->execute([$id]);
        
        // Удаляем пользователя
        $stmt = $db->prepare("DELETE FROM users WHERE application_id = ?");
        $stmt->execute([$id]);
        
        // Удаляем заявку
        $stmt = $db->prepare("DELETE FROM application WHERE id = ?");
        $stmt->execute([$id]);
        
        $db->commit();
        $message = "Данные успешно удалены.";
    } catch (Exception $e) {
        $db->rollBack();
        $message = "Ошибка при удалении.";
    }
}

// Получаем все заявки
$stmt = $db->query("
    SELECT a.*, u.login 
    FROM application a 
    LEFT JOIN users u ON a.id = u.application_id 
    ORDER BY a.id DESC
");
$applications = $stmt->fetchAll();

// Статистика по языкам
$stmt = $db->query("
    SELECT l.id, l.name, COUNT(al.application_id) as count 
    FROM languages l
    LEFT JOIN application_languages al ON l.id = al.language_id
    GROUP BY l.id
    ORDER BY count DESC
");
$statistics = $stmt->fetchAll();

$langs_list = [
    1 => 'Pascal', 2 => 'C', 3 => 'C++', 4 => 'JavaScript',
    5 => 'PHP', 6 => 'Python', 7 => 'Java', 8 => 'Haskell',
    9 => 'Clojure', 10 => 'Prolog', 11 => 'Scala', 12 => 'Go'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Администрирование</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1300px; margin: 0 auto; }
        
        .header {
            background: white;
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header h1 { color: #667eea; font-size: 24px; }
        .header a {
            background: #667eea;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
        }
        .header a:hover { background: #764ba2; }
        
        .stats {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
        }
        .stats h2 { color: #333; margin-bottom: 15px; border-left: 4px solid #667eea; padding-left: 15px; }
        .stats-grid { display: flex; flex-wrap: wrap; gap: 12px; }
        .stat-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            text-align: center;
            min-width: 90px;
        }
        .stat-item .lang { font-size: 13px; }
        .stat-item .count { font-size: 28px; font-weight: bold; }
        
        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .table-wrapper {
            background: white;
            border-radius: 15px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #667eea;
            color: white;
            padding: 12px 10px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        tr:hover { background: #f5f5f5; }
        
        .btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 20px;
            color: white;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Администрирование</h1>
        <a href="index.php">Форма регистрации</a>
    </div>
    
    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <!-- Статистика -->
    <div class="stats">
        <h2>Статистика по языкам программирования</h2>
        <div class="stats-grid">
            <?php foreach ($statistics as $stat): ?>
                <div class="stat-item">
                    <div class="lang"><?= htmlspecialchars($stat['name']) ?></div>
                    <div class="count"><?= $stat['count'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Таблица данных -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Полное имя</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Дата рождения</th>
                    <th>Пол</th>
                    <th>Логин</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:40px;">Нет заявок</td></tr>
                <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['id']) ?></td>
                            <td><?= htmlspecialchars($app['fio']) ?></td>
                            <td><?= htmlspecialchars($app['email']) ?></td>
                            <td><?= htmlspecialchars($app['phone']) ?></td>
                            <td><?= htmlspecialchars($app['birth_date']) ?></td>
                            <td><?= $app['gender'] == 'male' ? 'Мужской' : 'Женский' ?></td>
                            <td><?= htmlspecialchars($app['login'] ?? '—') ?></td>
                            <td>
                                <a href="?delete=<?= $app['id'] ?>" 
                                   class="btn btn-delete" 
                                   onclick="return confirm('Удалить заявку?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="footer-note">
        Всего: <?= count($applications) ?> заявок | Вы вошли как admin
    </div>
</div>
</body>
</html>
