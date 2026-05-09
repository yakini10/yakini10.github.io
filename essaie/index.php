<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Ветеринарная клиника</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; 
            background: #f0f8f0; 
            padding: 15px; 
        }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #2e7d32; text-align: center; margin: 20px 0; font-size: 1.8rem; }
        .menu { display: flex; flex-direction: column; gap: 15px; }
        .menu-card { 
            background: white; 
            padding: 25px; 
            border-radius: 15px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            text-align: center; 
            transition: transform 0.2s;
        }
        .menu-card:active { transform: scale(0.98); }
        .menu-card a { 
            text-decoration: none; 
            color: #2e7d32; 
            font-size: 1.4rem; 
            font-weight: bold; 
            display: block;
        }
        .menu-card p { color: #666; margin-top: 8px; font-size: 0.9rem; }
        .emoji { font-size: 3rem; display: block; margin-bottom: 10px; }
        .footer { text-align: center; margin-top: 30px; color: #999; font-size: 0.8rem; }
        @media (min-width: 768px) {
            .menu { flex-direction: row; flex-wrap: wrap; justify-content: center; }
            .menu-card { width: 250px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🐾 Ветеринарная клиника</h1>
    
    <div class="menu">
        <div class="menu-card">
            <span class="emoji">🐕</span>
            <a href="animaux.php">Животные</a>
            <p>Управление животными</p>
        </div>
        
        <div class="menu-card">
            <span class="emoji">📅</span>
            <a href="visites.php">Визиты</a>
            <p>Журнал посещений</p>
        </div>
        <div class="menu-card">   <!-- NOUVEAU -->
            <span class="emoji">👥</span>
            <a href="proprietaires.php">Владельцы</a>
            <p>Список владельцев</p>
        </div>
    </div>
</div>
</body>
</html>
