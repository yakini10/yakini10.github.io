<?php
require_once 'config.php';

$proprietaires = $pdo->query("SELECT * FROM proprietaires ORDER BY nom")->fetchAll();
$animal = null;
$isModification = false;

// Если есть ID в URL, это режим изменения
if (isset($_GET['modifier_id'])) {
    $isModification = true;
    $stmt = $pdo->prepare("SELECT * FROM animaux WHERE id = ?");
    $stmt->execute([$_GET['modifier_id']]);
    $animal = $stmt->fetch();
    
    if (!$animal) {
        header('Location: index.php');
        exit();
    }
}

// Обработка формы (добавление ИЛИ изменение)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modifier_id']) && !empty($_POST['modifier_id'])) {
        // ИЗМЕНЕНИЕ
        $sql = "UPDATE animaux SET nom=?, type=?, age=?, couleur=?, id_proprietaire=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['nom'], 
            $_POST['type'], 
            $_POST['age'], 
            $_POST['couleur'], 
            $_POST['id_proprietaire'],
            $_POST['modifier_id']
        ]);
        $_SESSION['message'] = "Животное успешно изменено!";
    } else {
        // ДОБАВЛЕНИЕ
        $sql = "INSERT INTO animaux (nom, type, age, couleur, id_proprietaire) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['nom'], $_POST['type'], $_POST['age'], $_POST['couleur'], $_POST['id_proprietaire']]);
        $_SESSION['message'] = "Животное успешно добавлено!";
    }
    header('Location: index.php');
    exit();
}

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
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: <?= $couleurBouton ?>; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn { background: #666; text-decoration: none; color: white; padding: 10px 20px; display: inline-block; border-radius: 5px; margin-left: 10px; }
        h1 { color: <?= $couleurBouton ?>; }
        label { font-weight: bold; display: block; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1><?= $titre ?></h1>
    <form method="POST">
        <?php if($isModification): ?>
            <input type="hidden" name="modifier_id" value="<?= $animal['id'] ?>">
        <?php endif; ?>
        
        <label>Имя *</label>
        <input type="text" name="nom" value="<?= $animal ? htmlspecialchars($animal['nom']) : '' ?>" required>
        
        <label>Тип (собака, кошка, кролик...) *</label>
        <input type="text" name="type" value="<?= $animal ? htmlspecialchars($animal['type']) : '' ?>" required>
        
        <label>Возраст (лет)</label>
        <input type="number" name="age" value="<?= $animal ? $animal['age'] : '' ?>">
        
        <label>Цвет</label>
        <input type="text" name="couleur" value="<?= $animal ? htmlspecialchars($animal['couleur']) : '' ?>">
        
        <label>Владелец *</label>
        <select name="id_proprietaire" required>
            <option value="">-- Выберите владельца --</option>
            <?php foreach($proprietaires as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($animal && $animal['id_proprietaire'] == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <div style="margin-top: 20px;">
            <button type="submit"><?= $textBouton ?></button>
            <a href="index.php" class="btn">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>
