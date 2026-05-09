<?php
require_once 'config.php';

$animal = null;
$isModification = false;
$erreurs = [];

// Récupérer tous les animaux
$animaux = $pdo->query("
    SELECT a.*, p.prenom, p.nom as proprietaire_nom, p.telephone
    FROM animaux a
    LEFT JOIN proprietaires p ON a.id_proprietaire = p.id
    ORDER BY a.nom
")->fetchAll();

// Mode modification
if (isset($_GET['modifier_id'])) {
    $isModification = true;
    $stmt = $pdo->prepare("
        SELECT a.*, p.prenom as proprietaire_prenom, p.nom as proprietaire_nom, p.telephone as proprietaire_telephone, p.email
        FROM animaux a
        LEFT JOIN proprietaires p ON a.id_proprietaire = p.id
        WHERE a.id = ?
    ");
    $stmt->execute([$_GET['modifier_id']]);
    $animal = $stmt->fetch();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (empty($_POST['animal_nom'])) {
        $erreurs[] = "❌ Укажите имя животного!";
    }
    if (empty($_POST['proprietaire_nom'])) {
        $erreurs[] = "❌ Укажите фамилию владельца!";
    }
    
    if (empty($erreurs)) {
        // Gestion du propriétaire
        $prenom = $_POST['proprietaire_prenom'] ?? '';
        $nom = $_POST['proprietaire_nom'];
        $telephone = $_POST['proprietaire_telephone'] ?? '';
        $email = $_POST['proprietaire_email'] ?? '';
        
        $stmt = $pdo->prepare("SELECT id FROM proprietaires WHERE telephone = ? OR (prenom = ? AND nom = ?)");
        $stmt->execute([$telephone, $prenom, $nom]);
        $proprietaire = $stmt->fetch();
        
        if ($proprietaire) {
            $id_proprietaire = $proprietaire['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO proprietaires (prenom, nom, telephone, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$prenom, $nom, $telephone, $email]);
            $id_proprietaire = $pdo->lastInsertId();
        }
        
        if ($_POST['action'] === 'modifier' && !empty($_POST['animal_id'])) {
            $stmt = $pdo->prepare("UPDATE animaux SET nom=?, type=?, age=?, couleur=?, id_proprietaire=? WHERE id=?");
            $stmt->execute([$_POST['animal_nom'], $_POST['animal_type'], $_POST['animal_age'], $_POST['animal_couleur'], $id_proprietaire, $_POST['animal_id']]);
            $_SESSION['message'] = "✅ Животное изменено!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO animaux (nom, type, age, couleur, id_proprietaire) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['animal_nom'], $_POST['animal_type'], $_POST['animal_age'], $_POST['animal_couleur'], $id_proprietaire]);
            $_SESSION['message'] = "✅ Животное добавлено!";
        }
        header('Location: animaux.php');
        exit();
    }
}

// Suppression
if (isset($_GET['supprimer_id'])) {
    $stmt = $pdo->prepare("DELETE FROM animaux WHERE id = ?");
    $stmt->execute([$_GET['supprimer_id']]);
    $_SESSION['message'] = "🗑️ Животное удалено!";
    header('Location: animaux.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Животные - Ветеринарная клиника</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background: #f0f8f0; padding: 15px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #2e7d32; margin-bottom: 20px; font-size: 1.6rem; }
        h2 { color: #2e7d32; margin: 20px 0 15px; font-size: 1.3rem; border-bottom: 2px solid #2e7d32; padding-bottom: 5px; }
        .message { background: #c8e6c9; padding: 12px; border-radius: 10px; margin-bottom: 20px; color: #1b5e20; }
        .error { background: #ffebee; color: #c62828; padding: 12px; border-radius: 10px; margin-bottom: 20px; }
        .btn { background: #2e7d32; color: white; padding: 12px 20px; border: none; border-radius: 10px; font-size: 1rem; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .btn-warning { background: #f57c00; }
        .btn-danger { background: #c62828; }
        .btn-small { padding: 6px 12px; font-size: 0.8rem; margin: 2px; }
        .btn-block { width: 100%; margin-top: 15px; }
        input, select { width: 100%; padding: 12px; margin: 8px 0 15px; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; font-family: inherit; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        .required { color: #c62828; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; margin-top: 15px; }
        th, td { padding: 12px 8px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2e7d32; color: white; font-weight: normal; }
        tr:hover { background: #f5f5f5; }
        .card { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .nav { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .nav a { background: #2e7d32; color: white; padding: 10px 15px; text-decoration: none; border-radius: 25px; font-size: 0.9rem; }
        .actions { display: flex; flex-wrap: wrap; gap: 5px; }
        @media (max-width: 600px) {
            th, td { display: block; width: 100%; }
            tr { display: block; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 10px; }
            th { display: none; }
            td { border: none; padding: 8px 12px; }
            td:before { content: attr(data-label); font-weight: bold; display: inline-block; width: 120px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="index.php">🏠 Главная</a>
        <a href="visites.php">📅 Визиты</a>
    </div>
    
    <h1>🐕 Управление животными</h1>
    
    <?php if(isset($_SESSION['message'])): ?>
        <div class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    
    <?php if(!empty($erreurs)): ?>
        <div class="error"><?= implode('<br>', $erreurs) ?></div>
    <?php endif; ?>
    
    <!-- Formulaire d'ajout/modification -->
    <div class="card">
        <h2><?= $isModification ? '✏️ Редактирование' : '➕ Новое животное' ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $isModification ? 'modifier' : 'ajouter' ?>">
            <?php if($isModification): ?>
                <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">
            <?php endif; ?>
            
            <label>Имя животного <span class="required">*</span></label>
            <input type="text" name="animal_nom" value="<?= $animal ? htmlspecialchars($animal['nom']) : '' ?>" required>
            
            <label>Тип</label>
            <input type="text" name="animal_type" value="<?= $animal ? htmlspecialchars($animal['type']) : '' ?>" placeholder="собака, кошка, кролик...">
            
            <label>Возраст (лет)</label>
            <input type="number" name="animal_age" value="<?= $animal ? $animal['age'] : '' ?>">
            
            <label>Цвет</label>
            <input type="text" name="animal_couleur" value="<?= $animal ? htmlspecialchars($animal['couleur']) : '' ?>">
            
            <label>Имя владельца</label>
            <input type="text" name="proprietaire_prenom" value="<?= $animal ? htmlspecialchars($animal['proprietaire_prenom'] ?? '') : '' ?>">
            
            <label>Фамилия владельца <span class="required">*</span></label>
            <input type="text" name="proprietaire_nom" value="<?= $animal ? htmlspecialchars($animal['proprietaire_nom'] ?? '') : '' ?>" required>
            
            <label>Телефон владельца</label>
            <input type="tel" name="proprietaire_telephone" value="<?= $animal ? htmlspecialchars($animal['proprietaire_telephone'] ?? '') : '' ?>">
            
            <label>Email владельца</label>
            <input type="email" name="proprietaire_email" value="<?= $animal ? htmlspecialchars($animal['email'] ?? '') : '' ?>">
            
            <button type="submit" class="btn btn-block">💾 <?= $isModification ? 'Сохранить изменения' : 'Добавить животное' ?></button>
            <?php if($isModification): ?>
                <a href="animaux.php" class="btn btn-block" style="background:#666; text-align:center; margin-top:10px;">❌ Отмена</a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Liste des animaux -->
    <h2>📋 Список животных</h2>
    <table>
        <thead>
            <tr><th>Имя</th><th>Тип</th><th>Возраст</th><th>Цвет</th><th>Владелец</th><th>Телефон</th><th>Действия</th></tr>
        </thead>
        <tbody>
            <?php foreach($animaux as $a): ?>
            <tr>
                <td data-label="Имя"><?= htmlspecialchars($a['nom']) ?></td>
                <td data-label="Тип"><?= htmlspecialchars($a['type']) ?></td>
                <td data-label="Возраст"><?= $a['age'] ?> лет</td>
                <td data-label="Цвет"><?= htmlspecialchars($a['couleur']) ?></td>
                <td data-label="Владелец"><?= htmlspecialchars($a['prenom'] . ' ' . $a['proprietaire_nom']) ?></td>
                <td data-label="Телефон"><?= htmlspecialchars($a['telephone']) ?></td>
                <td data-label="Действия" class="actions">
                    <a href="?modifier_id=<?= $a['id'] ?>" class="btn btn-small btn-warning">✏️</a>
                    <a href="?supprimer_id=<?= $a['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Удалить?')">🗑️</a>
                    <a href="visites.php?animal_id=<?= $a['id'] ?>&animal_nom=<?= urlencode($a['nom']) ?>" class="btn btn-small" style="background:#17a2b8;">📅</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
