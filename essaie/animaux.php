<?php
require_once 'config.php';

$animaux = $pdo->query("
    SELECT a.*, p.nom as proprietaire_nom, p.prenom 
    FROM animaux a 
    LEFT JOIN proprietaires p ON a.id_proprietaire = p.id 
    ORDER BY a.nom
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Liste des animaux</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: #f0f8f0; 
            padding: 20px; 
        }
        
        .container { 
            max-width: 1300px; 
            margin: 0 auto; 
        }
        
        .header { 
            background: #2e7d32; 
            color: white; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 { 
            font-size: 1.5rem;
        }
        
        .btn-back {
            background: #1b5e20;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .btn-back:hover {
            background: #0d3b0f;
        }
        
        .card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
        }
        
        .card h2 { 
            color: #2e7d32; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #2e7d32; 
            padding-bottom: 10px; 
        }
        
        .btn-small { 
            padding: 5px 10px; 
            font-size: 12px; 
            margin: 2px; 
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: opacity 0.3s;
        }
        
        .btn-small:hover {
            opacity: 0.8;
        }
        
        .btn-warning { 
            background: #f57c00; 
            color: white;
        }
        
        .btn-danger { 
            background: #c62828; 
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .animaux-liste {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .animal-item {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            transition: 0.2s;
        }
        
        .animal-item:hover {
            background: #f0f0f0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .animal-info {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: baseline;
        }
        
        .animal-nom {
            font-size: 18px;
            font-weight: bold;
            color: #2e7d32;
            min-width: 120px;
        }
        
        .animal-detail {
            color: #555;
            font-size: 14px;
        }
        
        .animal-detail span {
            background: #e8e8e8;
            padding: 3px 8px;
            border-radius: 12px;
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .animal-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: flex-start;
            }
            
            .animal-info {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🐕 Liste des animaux</h1>
        <a href="index.php" class="btn-back">← Retour à l'accueil</a>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="message" style="background: #c8e6c9; padding: 10px; border-radius: 5px; margin-bottom: 20px; color: #1b5e20;">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>📋 Tous les animaux</h2>
        <div class="animaux-liste">
            <?php if(empty($animaux)): ?>
                <p style="text-align: center; color: #999;">Aucun animal enregistré</p>
            <?php else: ?>
                <?php foreach($animaux as $animal): ?>
                <div class="animal-item">
                    <div class="animal-info">
                        <div class="animal-nom"><?= htmlspecialchars($animal['nom']) ?></div>
                        <div class="animal-detail">
                            <span><?= htmlspecialchars($animal['type']) ?></span>
                            <span><?= $animal['age'] ?> ans</span>
                            <span><?= htmlspecialchars($animal['couleur']) ?></span>
                            <span>👤 <?= htmlspecialchars($animal['prenom'] . ' ' . $animal['proprietaire_nom']) ?></span>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <a href="form.php?modifier_id=<?= $animal['id'] ?>" class="btn-small btn-warning">✏️ Modifier</a>
                        <a href="supprimer.php?id=<?= $animal['id'] ?>" class="btn-small btn-danger" onclick="return confirm('Supprimer <?= htmlspecialchars($animal['nom']) ?> ?')">🗑️ Supprimer</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
