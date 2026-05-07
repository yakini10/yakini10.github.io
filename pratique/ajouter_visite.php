<?php
require_once 'config.php';

$animaux = $pdo->query("SELECT a.id, a.nom, p.prenom, p.nom as nom_famille 
                        FROM animaux a
                        LEFT JOIN proprietaires p ON a.id_proprietaire = p.id
                        ORDER BY a.nom")->fetchAll();

$maladies = $pdo->query("SELECT * FROM maladies ORDER BY nom_maladie")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO visites (id_animal, date_visite, symptomes, id_maladie, traitement) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['id_animal'],
        $_POST['date_visite'],
        $_POST['symptomes'],
        $_POST['id_maladie'],
        $_POST['traitement']
    ]);
    $_SESSION['message'] = "Визит успешно добавлен!";
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить визит</title>
    <style>
        body { font-family: 'Segoe UI', Arial; background: #f0f8f0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; }
        input, select, textarea { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 5px; }
        textarea { min-height: 80px; }
        button { background: #17a2b8; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn { background: #666; text-decoration: none; color: white; padding: 10px 20px; display: inline-block; border-radius: 5px; }
        h1 { color: #17a2b8; text-align: center; }
        label { font-weight: bold; display: block; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1> Добавить новый визит</h1>
    
    <form method="POST">
        <label>Животное *</label>
        <select name="id_animal" required>
            <option value="">-- Выберите животное --</option>
            <?php foreach($animaux as $a): ?>
                <option value="<?= $a['id'] ?>">
                    <?= htmlspecialchars($a['nom']) ?> (владелец: <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom_famille']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Дата визита *</label>
        <input type="date" name="date_visite" value="<?= date('Y-m-d') ?>" required>
        
        <label>Симптомы (жалобы)</label>
        <textarea name="symptomes" placeholder="Опишите симптомы животного..."></textarea>
        
        <label>Диагноз (болезнь)</label>
        <select name="id_maladie">
            <option value="">-- Не выбран --</option>
            <?php foreach($maladies as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nom_maladie']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label>Назначенное лечение</label>
        <textarea name="traitement" placeholder="Какое лечение назначено?"></textarea>
        
        <div style="text-align: center; margin-top: 20px;">
            <button type="submit"> Добавить визит</button>
            <a href="index.php" class="btn">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>
