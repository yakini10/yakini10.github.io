<?php
require_once 'config.php';

// Récupérer tous les animaux pour le formulaire
$animaux = $pdo->query("SELECT id, nom FROM animaux ORDER BY nom")->fetchAll();

// Récupérer toutes les maladies pour le filtre
$maladies = $pdo->query("SELECT * FROM maladies ORDER BY nom_maladie")->fetchAll();

// Récupérer toutes les visites
$visites = $pdo->query("
    SELECT v.*, a.nom as animal_nom, m.nom_maladie 
    FROM visites v
    JOIN animaux a ON v.id_animal = a.id
    LEFT JOIN maladies m ON v.id_maladie = m.id
    ORDER BY v.date_visite DESC
")->fetchAll();

// Filtrage
$visites_filtrees = [];
$maladie_selectionnee = null;
if (isset($_POST['filtrer'])) {
    $maladie_selectionnee = $_POST['maladie_id'];
    $stmt = $pdo->prepare("
        SELECT v.*, a.nom as animal_nom, m.nom_maladie 
        FROM visites v
        JOIN animaux a ON v.id_animal = a.id
        LEFT JOIN maladies m ON v.id_maladie = m.id
        WHERE v.id_maladie = ?
        ORDER BY v.date_visite DESC
    ");
    $stmt->execute([$maladie_selectionnee]);
    $visites_filtrees = $stmt->fetchAll();
}

// Ajout d'une visite
$erreurs = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_visite'])) {
    if (empty($_POST['id_animal'])) {
        $erreurs[] = "❌ Выберите животное!";
    }
    if (empty($_POST['date_visite'])) {
        $erreurs[] = "❌ Укажите дату!";
    }
    if (empty($_POST['symptomes'])) {
        $erreurs[] = "❌ Укажите симптомы!";
    }
    if (empty($_POST['traitement'])) {
        $erreurs[] = "❌ Укажите лечение!";
    }
    
    if (empty($erreurs)) {
        $id_maladie = !empty($_POST['id_maladie']) ? $_POST['id_maladie'] : NULL;
        $stmt = $pdo->prepare("INSERT INTO visites (id_animal, date_visite, symptomes, id_maladie, traitement) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['id_animal'],
            $_POST['date_visite'],
            $_POST['symptomes'],
            $id_maladie,
            $_POST['traitement']
        ]);
        $_SESSION['message'] = "✅ Визит добавлен!";
        header('Location: visites.php');
        exit();
    }
}

// Récupérer l'animal sélectionné depuis l'URL
$animal_id = $_GET['animal_id'] ?? '';
$animal_nom = $_GET['animal_nom'] ?? '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Визиты - Ветеринарная клиника</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background: #f0f8f0; padding: 15px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #2e7d32; margin-bottom: 20px; font-size: 1.6rem; }
        h2 { color: #2e7d32; margin: 20px 0 15px; font-size: 1.3rem; border-bottom: 2px solid #2e7d32; padding-bottom: 5px; }
        .message { background: #c8e6c9; padding: 12px; border-radius: 10px; margin-bottom: 20px; color: #1b5e20; }
        .error { background: #ffebee; color: #c62828; padding: 12px; border-radius: 10px; margin-bottom: 20px; }
        .btn { background: #2e7d32; color: white; padding: 12px 20px; border: none; border-radius: 10px; font-size: 1rem; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .btn-block { width: 100%; margin-top: 15px; }
        input, select, textarea { width: 100%; padding: 12px; margin: 8px 0 15px; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; font-family: inherit; }
        textarea { min-height: 80px; resize: vertical; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        .required { color: #c62828; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; margin-top: 15px; }
        th, td { padding: 12px 8px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2e7d32; color: white; font-weight: normal; }
        tr:hover { background: #f5f5f5; }
        .card { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .nav { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .nav a { background: #2e7d32; color: white; padding: 10px 15px; text-decoration: none; border-radius: 25px; font-size: 0.9rem; }
        .filter-form { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; }
        .filter-form select { flex: 2; margin: 0; }
        .filter-form button { flex: 1; margin: 0; }
        @media (max-width: 600px) {
            .filter-form { flex-direction: column; }
            .filter-form select, .filter-form button { width: 100%; }
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
    </div>
    
    <h1>📅 Управление визитами</h1>
    
    <?php if(isset($_SESSION['message'])): ?>
        <div class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    
    <?php if(!empty($erreurs)): ?>
        <div class="error"><?= implode('<br>', $erreurs) ?></div>
    <?php endif; ?>
    
    <!-- Formulaire d'ajout de visite -->
    <div class="card">
        <h2>➕ Новый визит</h2>
        <?php if($animal_nom): ?>
            <p style="background:#e3f2fd; padding:10px; border-radius:10px; margin-bottom:15px;">🐕 Животное: <strong><?= htmlspecialchars($animal_nom) ?></strong></p>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="ajouter_visite" value="1">
            
            <label>Животное <span class="required">*</span></label>
            <select name="id_animal" required>
                <option value="">-- Выберите животное --</option>
                <?php foreach($animaux as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($animal_id == $a['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label>Дата визита <span class="required">*</span></label>
            <input type="date" name="date_visite" value="<?= date('Y-m-d') ?>" required>
            
            <label>Симптомы <span class="required">*</span></label>
            <textarea name="symptomes" placeholder="Опишите симптомы..." required></textarea>
            
            <label>Диагноз (болезнь)</label>
            <select name="id_maladie">
                <option value="">-- Не выбран --</option>
                <?php foreach($maladies as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nom_maladie']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <label>Лечение <span class="required">*</span></label>
            <textarea name="traitement" placeholder="Назначенное лечение..." required></textarea>
            
            <button type="submit" class="btn btn-block">💾 Добавить визит</button>
        </form>
    </div>
    
    <!-- Filtre par maladie -->
    <div class="card">
        <h2>🔍 Фильтр по болезни</h2>
        <form method="POST" class="filter-form">
            <select name="maladie_id">
                <option value="">-- Все болезни --</option>
                <?php foreach($maladies as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($maladie_selectionnee == $m['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nom_maladie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="filtrer" class="btn">🔍 Показать</button>
            <a href="visites.php" class="btn" style="background:#666;">❌ Сбросить</a>
        </form>
    </div>
    
    <!-- Liste des visites -->
    <h2>📋 Журнал визитов</h2>
    <?php
    $affichage_visites = isset($_POST['filtrer']) ? $visites_filtrees : $visites;
    ?>
    <?php if(empty($affichage_visites)): ?>
        <div class="card" style="text-align:center; color:#666;">Нет визитов</div>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Дата</th><th>Животное</th><th>Симптомы</th><th>Болезнь</th><th>Лечение</th></tr>
            </thead>
            <tbody>
                <?php foreach($affichage_visites as $v): ?>
                <tr>
                    <td data-label="Дата"><?= $v['date_visite'] ?></td>
                    <td data-label="Животное"><?= htmlspecialchars($v['animal_nom']) ?></td>
                    <td data-label="Симптомы"><?= htmlspecialchars($v['symptomes']) ?></td>
                    <td data-label="Болезнь"><?= htmlspecialchars($v['nom_maladie'] ?? '—') ?></td>
                    <td data-label="Лечение"><?= htmlspecialchars($v['traitement']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
