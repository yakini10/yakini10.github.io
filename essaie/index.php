<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Clinique Vétérinaire - Accueil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* En-tête */
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            padding: 20px;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        /* Message de succès */
        .message {
            background: #4caf50;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Grille des cartes */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        /* Cartes */
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: block;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        /* Icônes */
        .card-icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
        }
        
        .card h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #4a5568;
        }
        
        .card p {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        /* Couleurs spécifiques pour chaque carte */
        .card-animaux {
            border-bottom: 4px solid #4299e1;
        }
        
        .card-visites {
            border-bottom: 4px solid #48bb78;
        }
        
        .card-ajout-animal {
            border-bottom: 4px solid #ed8936;
        }
        
        .card-ajout-visite {
            border-bottom: 4px solid #9f7aea;
        }
        
        .card-proprietaires {
            border-bottom: 4px solid #f687b3;
        }
        
        .card-maladies {
            border-bottom: 4px solid #f56565;
        }
        
        .card-filtrer {
            border-bottom: 4px solid #38b2ac;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            color: white;
            padding: 20px;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        /* Responsive pour très petits écrans */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .header p {
                font-size: 0.9rem;
            }
            
            .grid {
                gap: 15px;
            }
            
            .card {
                padding: 20px 15px;
            }
            
            .card-icon {
                font-size: 2.5rem;
            }
            
            .card h2 {
                font-size: 1.2rem;
            }
            
            .card p {
                font-size: 0.85rem;
            }
        }
        
        /* Pour écrans moyens (tablettes) */
        @media (min-width: 481px) and (max-width: 768px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
        
        /* Pour grands écrans */
        @media (min-width: 1200px) {
            .grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🐾 Clinique Vétérinaire</h1>
        <p>Gérez facilement vos patients à quatre pattes</p>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="message">
            ✅ <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="grid">
        <!-- Lien vers la liste des animaux -->
        <a href="liste_animaux.php" class="card card-animaux">
            <div class="card-icon">🐕‍🦺</div>
            <h2>Liste des animaux</h2>
            <p>Consulter, modifier ou supprimer les dossiers des animaux</p>
        </a>

        <!-- Lien vers le journal des visites -->
        <a href="liste_visites.php" class="card card-visites">
            <div class="card-icon">📋</div>
            <h2>Journal des visites</h2>
            <p>Historique complet de toutes les consultations</p>
        </a>

        <!-- Lien pour ajouter un animal -->
        <a href="form.php" class="card card-ajout-animal">
            <div class="card-icon">➕🐕</div>
            <h2>Ajouter un animal</h2>
            <p>Enregistrer un nouveau patient et son propriétaire</p>
        </a>

        <!-- Lien pour ajouter une visite -->
        <a href="ajouter_visite.php" class="card card-ajout-visite">
            <div class="card-icon">🏥</div>
            <h2>Ajouter une visite</h2>
            <p>Enregistrer une nouvelle consultation</p>
        </a>

        <!-- Lien vers la liste des propriétaires -->
        <a href="liste_proprietaires.php" class="card card-proprietaires">
            <div class="card-icon">👨‍👩‍👧‍👦</div>
            <h2>Liste des propriétaires</h2>
            <p>Consulter tous les propriétaires et leurs animaux</p>
        </a>

        <!-- Lien vers la liste des maladies -->
        <a href="liste_maladies.php" class="card card-maladies">
            <div class="card-icon">🩺</div>
            <h2>Liste des maladies</h2>
            <p>Consulter le catalogue des maladies</p>
        </a>

        <!-- Lien vers le filtre des visites par maladie -->
        <a href="filtrer_visites.php" class="card card-filtrer">
            <div class="card-icon">🔍</div>
            <h2>Filtrer les visites</h2>
            <p>Rechercher des visites par type de maladie</p>
        </a>
    </div>

    <div class="footer">
        <p>© 2024 Clinique Vétérinaire - Application de gestion</p>
    </div>
</div>
</body>
</html>
