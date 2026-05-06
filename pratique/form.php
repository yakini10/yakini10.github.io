<?php
// Подключение к базе данных
require_once 'config.php';

$animal = null;
$isModification = false;

// Проверяем, есть ли ID в URL (режим редактирования)
if (isset($_GET['modifier_id'])) {
    $isModification = true;
    $stmt = $pdo->prepare("SELECT * FROM animaux WHERE id = ?");
    $stmt->execute([$_GET['modifier_id']]);
    $animal = $stmt->fetch();
    
    // Если животное не найдено, возвращаемся на главную
    if (!$animal) {
        header('Location: index.php');
        exit();
    }
}

// Обработка отправленной формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Получаем данные владельца из формы
    $prenom = $_POST['proprietaire_prenom'];
    $nom = $_POST['proprietaire_nom'];
    $telephone = $_POST['proprietaire_telephone'];
    $email = $_POST['proprietaire_email'];
    
    // Проверяем, существует ли уже такой владелец в базе
    $stmt = $pdo->prepare("SELECT id FROM proprietaires WHERE prenom = ? AND nom = ?");
    $stmt->execute([$prenom, $nom]);
    $proprietaire = $stmt->fetch();
    
    if ($proprietaire) {
        // Владелец найден, берем его ID
        $id_proprietaire = $proprietaire['id'];
    } else {
        // Владелец не найден, создаем нового
        $stmt = $pdo->prepare("INSERT INTO proprietaires (prenom, nom, telephone, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$prenom, $nom, $telephone, $email]);
        $id_proprietaire = $pdo->lastInsertId();
    }
    
    // Добавляем или обновляем животное
    if (isset($_POST['modifier_id']) && !empty($_POST['modifier_id'])) {
        // Режим редактирования: обновляем существующее животное
        $sql = "UPDATE animaux SET nom=?, type=?, age=?, couleur=?, id_proprietaire=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['animal_nom'],
            $_POST['animal_type'],
            $_POST['animal_age'],
            $_POST['animal_couleur'],
            $id_proprietaire,
            $_POST['modifier_id']
        ]);
        $id_animal = $_POST['modifier_id'];
        $_SESSION['message'] = "Животное успешно изменено!";
    } else {
        // Режим добавления: создаем новое животное
        $sql = "INSERT INTO animaux (nom, type, age, couleur, id_proprietaire) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['animal_nom'],
            $_POST['animal_type'],
            $_POST['animal_age'],
            $_POST['animal_couleur'],
            $id_proprietaire
        ]);
        $id_animal = $pdo->lastInsertId();
        $_SESSION['message'] = "Животное успешно добавлено!";
    }
    
    // Добавляем визит, если указаны симптомы
    if (!empty($_POST['visite_symptomes'])) {
        $stmt = $pdo->prepare("INSERT INTO visites (id_animal, date_visite, symptomes, traitement) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $id_animal,
            $_POST['visite_date'],
            $_POST['visite_symptomes'],
            $_POST['visite_traitement']
        ]);
        $_SESSION['message'] .= " Визит добавлен!";
    }
    
    // Возвращаемся на главную страницу
    header('Location: index.php');
    exit();
}

// Определяем заголовок и цвет кнопки в зависимости от режима
$titre = $isModification ? " Изменить животное" : " Добавить животное";
$couleurBouton = $isModification ? "#f57c00" : "#2e7d32";
$textBouton = $isModification ? " Сохранить изменения" : " Добавить";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $titre ?></title>
    <style>
        body { font-family: 'Segoe UI', Arial; background: #f0f8f0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
        textarea { resize: vertical; min-height: 80px; }
        button { background: <?= $couleurBouton ?>; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn { background: #666; text-decoration: none; color: white; padding: 10px 20px; display: inline-block; border-radius: 5px; margin-left: 10px; }
        h1 { color: <?= $couleurBouton ?>; text-align: center; margin-bottom: 25px; }
        h3 { color: #2e7d32; margin-top: 20px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        .section { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1><?= $titre ?></h1>
    
    <form method="POST">
        <?php if($isModification): ?>
            <input type="hidden" name="modifier_id" value="<?= $animal['id'] ?>">
        <?php endif; ?>
        
        <!-- БЛОК: ИНФОРМАЦИЯ О ЖИВОТНОМ -->
        <div class="section">
            <h3> ИНФОРМАЦИЯ О ЖИВОТНОМ</h3>
            
            <label>Имя животного *</label>
            <input type="text" name="animal_nom" value="<?= $animal ? htmlspecialchars($animal['nom']) : '' ?>" required>
            
            <label>Тип (собака, кошка, кролик...) *</label>
            <input type="text" name="animal_type" value="<?= $animal ? htmlspecialchars($animal['type']) : '' ?>" required>
            
            <label>Возраст (лет)</label>
            <input type="number" name="animal_age" value="<?= $animal ? $animal['age'] : '' ?>">
            
            <label>Цвет</label>
            <input type="text" name="animal_couleur" value="<?= $animal ? htmlspecialchars($animal['couleur']) : '' ?>">
        </div>
        
        <!-- БЛОК: ИНФОРМАЦИЯ О ВЛАДЕЛЬЦЕ -->
        <div class="section">
            <h3> ИНФОРМАЦИЯ О ВЛАДЕЛЬЦЕ</h3>
            
            <label>Имя владельца *</label>
            <input type="text" name="proprietaire_prenom" value="<?= $animal ? htmlspecialchars($animal['prenom'] ?? '') : '' ?>" required>
            
            <label>Фамилия владельца *</label>
            <input type="text" name="proprietaire_nom" value="<?= $animal ? htmlspecialchars($animal['nom'] ?? '') : '' ?>" required>
            
            <label>Телефон владельца</label>
            <input type="text" name="proprietaire_telephone" value="<?= $animal ? htmlspecialchars($animal['telephone'] ?? '') : '' ?>">
            
            <label>Email владельца</label>
            <input type="email" name="proprietaire_email" value="<?= $animal ? htmlspecialchars($animal['email'] ?? '') : '' ?>">
        </div>
        
        <!-- БЛОК: ИНФОРМАЦИЯ О ВИЗИТЕ -->
        <div class="section">
            <h3> ИНФОРМАЦИЯ О ВИЗИТЕ</h3>
            
            <label>Дата визита</label>
            <input type="date" name="visite_date" value="<?= date('Y-m-d') ?>">
            
            <label>Симптомы (жалобы)</label>
            <textarea name="visite_symptomes" placeholder="Опишите симптомы животного..."></textarea>
            
            <label>Назначенное лечение</label>
            <textarea name="visite_traitement" placeholder="Какое лечение назначено?"></textarea>
            
            <p style="font-size: 12px; color: #666; margin-top: 5px;"> Если визит уже был, вы сможете добавить его позже отдельно.</p>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <button type="submit"><?= $textBouton ?></button>
            <a href="index.php" class="btn">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>
