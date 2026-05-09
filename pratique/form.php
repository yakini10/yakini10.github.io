<?php
require_once 'config.php';

$animal = null;
$isModification = false;
$erreurs = [];

// Проверяем, есть ли ID в URL (режим редактирования)
if (isset($_GET['modifier_id'])) {
    $isModification = true;
    $stmt = $pdo->prepare("
        SELECT a.*, 
               p.prenom as proprietaire_prenom, 
               p.nom as proprietaire_nom, 
               p.telephone as proprietaire_telephone, 
               p.email as proprietaire_email
        FROM animaux a
        LEFT JOIN proprietaires p ON a.id_proprietaire = p.id
        WHERE a.id = ?
    ");
    $stmt->execute([$_GET['modifier_id']]);
    $animal = $stmt->fetch();
    
    if (!$animal) {
        header('Location: index.php');
        exit();
    }
}

// Обработка отправленной формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (empty($_POST['animal_nom'])) {
        $erreurs[] = " Пожалуйста, укажите имя животного!";
    }
    if (empty($_POST['proprietaire_prenom'])) {
        $erreurs[] = " Пожалуйста, укажите имя владельца!";
    }
    if (empty($_POST['proprietaire_nom'])) {
        $erreurs[] = " Пожалуйста, укажите фамилию владельца!";
    }
    if (empty($_POST['proprietaire_telephone'])) {
        $erreurs[] = " Пожалуйста, укажите номер телефона владельца!";
    }
    
    if (empty($erreurs)) {
        // Получаем данные владельца из формы
        $prenom = $_POST['proprietaire_prenom'];
        $nom = $_POST['proprietaire_nom'];
        $telephone = $_POST['proprietaire_telephone'];
        $email = $_POST['proprietaire_email'];
        
        // Проверяем, существует ли уже такой владелец в базе
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
        
        // Добавляем или обновляем животное
        if (isset($_POST['modifier_id']) && !empty($_POST['modifier_id'])) {
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
            $_SESSION['message'] = " Животное успешно изменено!";
        } else {
            $sql = "INSERT INTO animaux (nom, type, age, couleur, id_proprietaire) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['animal_nom'],
                $_POST['animal_type'],
                $_POST['animal_age'],
                $_POST['animal_couleur'],
                $id_proprietaire
            ]);
            $_SESSION['message'] = " Животное успешно добавлено!";
        }
        
        header('Location: index.php');
        exit();
    }
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
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: <?= $couleurBouton ?>; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: <?= $isModification ? "#e65100" : "#1b5e20" ?>; }
        .btn { background: #666; text-decoration: none; color: white; padding: 10px 20px; display: inline-block; border-radius: 5px; margin-left: 10px; }
        .btn:hover { background: #555; }
        h1 { color: <?= $couleurBouton ?>; text-align: center; margin-bottom: 25px; }
        h3 { color: #2e7d32; margin-top: 20px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        .section { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .required { color: #c62828; }
        .error { color: #c62828; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h1><?= $titre ?></h1>
    
    <?php if (!empty($erreurs)): ?>
        <div class="error">
            <?php foreach($erreurs as $err): ?>
                <?= $err ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <?php if($isModification): ?>
            <input type="hidden" name="modifier_id" value="<?= $animal['id'] ?>">
        <?php endif; ?>
        
        <!-- БЛОК: ИНФОРМАЦИЯ О ЖИВОТНОМ -->
        <div class="section">
            <h3> ИНФОРМАЦИЯ О ЖИВОТНОМ</h3>
            
            <label>Имя животного <span class="required">*</span></label>
            <input type="text" name="animal_nom" value="<?= $animal ? htmlspecialchars($animal['nom']) : '' ?>" required>
            
            <label>Тип (собака, кошка, кролик...)</label>
            <input type="text" name="animal_type" value="<?= $animal ? htmlspecialchars($animal['type']) : '' ?>">
            
            <label>Возраст (лет)</label>
            <input type="number" name="animal_age" value="<?= $animal ? $animal['age'] : '' ?>">
            
            <label>Цвет</label>
            <input type="text" name="animal_couleur" value="<?= $animal ? htmlspecialchars($animal['couleur']) : '' ?>">
        </div>
        
        <!-- БЛОК: ИНФОРМАЦИЯ О ВЛАДЕЛЬЦЕ -->
        <div class="section">
            <h3> ИНФОРМАЦИЯ О ВЛАДЕЛЬЦЕ</h3>
            
            <label>Имя владельца <span class="required">*</span></label>
            <input type="text" name="proprietaire_prenom" value="<?= $animal ? htmlspecialchars($animal['proprietaire_prenom'] ?? '') : '' ?>" required>
            
            <label>Фамилия владельца <span class="required">*</span></label>
            <input type="text" name="proprietaire_nom" value="<?= $animal ? htmlspecialchars($animal['proprietaire_nom'] ?? '') : '' ?>" required>
            
            <label>Телефон владельца <span class="required">*</span></label>
            <input type="tel" name="proprietaire_telephone" value="<?= $animal ? htmlspecialchars($animal['proprietaire_telephone'] ?? '') : '' ?>" required>
            
            <label>Email владельца</label>
            <input type="email" name="proprietaire_email" value="<?= $animal ? htmlspecialchars($animal['proprietaire_email'] ?? '') : '' ?>">
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <button type="submit"><?= $textBouton ?></button>
            <a href="index.php" class="btn">Отмена</a>
        </div>
    </form>
    
    <?php if($isModification && $animal): ?>
        <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 8px; text-align: center;">
            <p><strong>Хотите добавить новый визит для этого животного?</strong></p>
            <a href="ajouter_visite.php?animal_id=<?= $animal['id'] ?>&animal_nom=<?= urlencode($animal['nom']) ?>" class="btn" style="background: #2e7d32;"> Добавить визит</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
