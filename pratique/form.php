<?php
require_once 'config.php';

$proprietaires = $pdo->query("SELECT * FROM proprietaires ORDER BY nom")->fetchAll();
$animal = null;
$isModification = false;

// Si on a un ID dans l'URL, c'est une modification
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

// Traitement du formulaire (ajout OU modification)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modifier_id']) && !empty($_POST['modifier_id'])) {
        // MODIFICATION
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
        $_SESSION['message'] = "Animal modifié avec succès !";
    } else {
        // AJOUT
        $sql = "INSERT INTO animaux (nom, type, age, couleur, id_proprietaire) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['nom'], $_POST['type'], $_POST['age'], $_POST['couleur'], $_POST['id_proprietaire']]);
        $_SESSION['message'] = "Animal ajouté avec succès !";
    }
    header('Location: index.php');
    exit();
}

$titre = $isModification ? "✏️ Modifier un animal" : "➕ Ajouter un animal";
$couleurBouton = $isModification ? "#f57c00" : "#2e7d32";
$textBouton = $isModification ? "💾 Mettre à jour" : "💾 Enregistrer";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $titre ?></title>
    <style>
        body { font-family: Arial; background: #f0f8f0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        input, select { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: <?= $couleurBouton ?>; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn { background: #666; text-decoration: none; color: white; padding: 10px 20px; display: inline-block; border-radius: 5px; }
        h1 { color: <?= $couleurBouton ?>; }
    </style>
</head>
<body>
<div class="container">
    <h1><?= $titre ?></h1>
    <form method="POST">
        <?php if($isModification): ?>
            <input type="hidden" name="modifier_id" value="<?= $animal['id'] ?>">
        <?php endif; ?>
        
        <label>Nom *</label>
        <input type="text" name="nom" value="<?= $animal ? htmlspecialchars($animal['nom']) : '' ?>" required>
        
        <label>Type * (chien, chat, lapin...)</label>
        <input type="text" name="type" value="<?= $animal ? htmlspecialchars($animal['type']) : '' ?>" required>
        
        <label>Âge (années)</label>
        <input type="number" name="age" value="<?= $animal ? $animal['age'] : '' ?>">
        
        <label>Couleur</label>
        <input type="text" name="couleur" value="<?= $animal ? htmlspecialchars($animal['couleur']) : '' ?>">
        
        <label>Propriétaire *</label>
        <select name="id_proprietaire" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach($proprietaires as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($animal && $animal['id_proprietaire'] == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit"><?= $textBouton ?></button>
        <a href="index.php" class="btn">Annuler</a>
    </form>
</div>
</body>
</html>
