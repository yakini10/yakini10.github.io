<?php
require_once 'config.php';

$proprietaires = $pdo->query("
    SELECT p.*, COUNT(a.id) as nb_animaux
    FROM proprietaires p
    LEFT JOIN animaux a ON p.id = a.id_proprietaire
    GROUP BY p.id
    ORDER BY p.nom
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Владельцы - Ветеринарная клиника</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background: #f0f8f0; padding: 15px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #2e7d32; margin-bottom: 20px; font-size: 1.6rem; }
        .nav { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .nav a { background: #2e7d32; color: white; padding: 10px 15px; text-decoration: none; border-radius: 25px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        th, td { padding: 12px 8px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2e7d32; color: white; font-weight: normal; }
        tr:hover { background: #f5f5f5; }
        .badge { background: #c8e6c9; color: #2e7d32; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; }
        @media (max-width: 600px) {
            th, td { display: block; width: 100%; }
            tr { display: block; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 10px; }
            th { display: none; }
            td { border: none; padding: 8px 12px; }
            td:before { content: attr(data-label); font-weight: bold; display: inline-block; width: 100px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="index.php">🏠 Главная</a>
        <a href="animaux.php">🐕 Животные</a>
        <a href="visites.php">📅 Визиты</a>
    </div>
    
    <h1>👥 Владельцы животных</h1>
    
    <table>
        <thead>
            <tr><th>ID</th><th>Имя</th><th>Фамилия</th><th>Телефон</th><th>Email</th><th>Животных</th></tr>
        </thead>
        <tbody>
            <?php foreach($proprietaires as $p): ?>
            <tr>
                <td data-label="ID"><?= $p['id'] ?></td>
                <td data-label="Имя"><?= htmlspecialchars($p['prenom']) ?></td>
                <td data-label="Фамилия"><?= htmlspecialchars($p['nom']) ?></td>
                <td data-label="Телефон"><?= htmlspecialchars($p['telephone']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($p['email']) ?></td>
                <td data-label="Животных"><span class="badge"><?= $p['nb_animaux'] ?> 🐕</span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
