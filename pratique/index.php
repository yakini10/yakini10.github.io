<?php
require_once 'config.php';

$pdo->exec("DELETE FROM proprietaires WHERE id NOT IN (SELECT IFNULL(id_proprietaire, 0) FROM animaux)");

// Получить всех животных
$animaux = $pdo->query("
    SELECT a.*, p.nom as proprietaire_nom, p.prenom 
    FROM animaux a 
    LEFT JOIN proprietaires p ON a.id_proprietaire = p.id 
    ORDER BY a.nom
")->fetchAll();

// Получить все болезни для фильтра
$maladies = $pdo->query("SELECT * FROM maladies ORDER BY nom_maladie")->fetchAll();

// Фильтрация визитов по болезни
$visites_filtrees = [];
$maladie_selectionnee = null;
if (isset($_POST['filtrer'])) {
    $maladie_selectionnee = $_POST['maladie_id'];
    $stmt = $pdo->prepare("
        SELECT v.*, a.nom as animal_nom, m.nom_maladie 
        FROM visites v
        JOIN animaux a ON v.id_animal = a.id
        JOIN maladies m ON v.id_maladie = m.id
        WHERE v.id_maladie = ?
        ORDER BY v.date_visite DESC
    ");
    $stmt->execute([$maladie_selectionnee]);
    $visites_filtrees = $stmt->fetchAll();
}

// Получить все визиты
$visites = $pdo->query("
    SELECT v.*, a.nom as animal_nom, m.nom_maladie 
    FROM visites v
    JOIN animaux a ON v.id_animal = a.id
    LEFT JOIN maladies m ON v.id_maladie = m.id
    ORDER BY v.date_visite DESC
")->fetchAll();

// Получить список владельцев с количеством животных
$proprietaires_list = $pdo->query("
    SELECT p.*, 
           COUNT(a.id) as nombre_animaux
    FROM proprietaires p
    LEFT JOIN animaux a ON p.id = a.id_proprietaire
    GROUP BY p.id
    ORDER BY p.nom
")->fetchAll();
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
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: #f0f8f0; 
            padding: 10px; 
            padding-top: 80px; 
        }

        .container { 
            max-width: 1300px; 
            margin: 0 auto; 
            width: 100%;
        }

        .nav-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #2e7d32;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            padding: 10px 15px;
        }
        
        .nav-fixed .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .nav-fixed h1 {
            color: white;
            font-size: 1.2rem;
            margin: 0;
        }
        
        /* ССЫЛКИ НАВИГАЦИИ */
        .nav-fixed .nav {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .nav-fixed .nav a {
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            background: #1b5e20;
            border-radius: 5px;
            transition: 0.3s;
            font-size: 13px;
            white-space: nowrap;
        }
        
        .nav-fixed .nav a:hover {
            background: #0d3b0f;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 120px;
                padding-left: 8px;
                padding-right: 8px;
            }
            
            .nav-fixed {
                padding: 8px 12px;
            }
            
            .nav-fixed h1 {
                font-size: 1rem;
                text-align: center;
                width: 100%;
            }
            
            .nav-fixed .container {
                flex-direction: column;
                gap: 8px;
            }
            
            .nav-fixed .nav {
                width: 100%;
                justify-content: center;
            }
            
            .nav-fixed .nav a {
                font-size: 11px;
                padding: 5px 8px;
                white-space: nowrap;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding-top: 130px;
            }
            
            .nav-fixed .nav {
                gap: 5px;
            }
            
            .nav-fixed .nav a {
                font-size: 10px;
                padding: 4px 6px;
            }
        }
        
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }
        
        .card { 
            background: white; 
            border-radius: 10px; 
            padding: 15px; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto; 
        }
        
        .card h2 { 
            color: #2e7d32; 
            margin-bottom: 12px; 
            border-bottom: 2px solid #2e7d32; 
            padding-bottom: 8px; 
            font-size: 1.3rem;
        }
        
        .form-group { margin-bottom: 12px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; font-size: 14px; }
        input, select, textarea { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 16px; 
        }
        
        button, .btn { 
            background: #2e7d32; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            border-radius: 5px; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-block;
            font-size: 14px;
            text-align: center;
        }
        
        .btn-danger { background: #c62828; }
        .btn-warning { background: #f57c00; }
        .btn-small { padding: 6px 12px; font-size: 12px; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
            min-width: 500px; 
        
        th, td { 
            padding: 8px; 
            text-align: left; 
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        
        th { 
            background: #2e7d32; 
            color: white;
            font-size: 12px;
        }
        
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .message { 
            background: #c8e6c9; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 15px; 
            color: #1b5e20; 
            border-left: 4px solid #2e7d32;
            font-size: 14px;
        }
        
        .row { 
            display: flex; 
            gap: 15px; 
            flex-wrap: wrap; 
        }
        
        .col { 
            flex: 1; 
            min-width: 200px; 
        }
        .animaux-liste {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 15px;
        }

        .animal-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .animal-info {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: baseline;
            flex: 1;
        }
        
        .animal-nom {
            font-size: 16px;
            font-weight: bold;
            color: #2e7d32;
            min-width: 100px;
        }
        
        .animal-detail {
            color: #555;
            font-size: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .animal-detail span {
            background: #e8e8e8;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
        
        .badge {
            display: inline-block;
            background: #2e7d32;
            color: white;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            margin-left: 5px;
        }
        
        @media (max-width: 768px) {
            .card {
                padding: 12px;
                border-radius: 8px;
            }
            
            .card h2 {
                font-size: 1.2rem;
            }
            
            .col { 
                flex: 100%; 
                min-width: auto;
            }
            
            .animal-item {
                flex-direction: column;  /* Passe en colonne sur mobile */
                align-items: stretch;
            }
            
            .animal-info {
                flex-direction: column;
                gap: 8px;
            }
            
            .animal-nom {
                font-size: 15px;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: stretch;
            }
            
            .action-buttons a {
                flex: 1;
                text-align: center;
            }
            
            button, .btn {
                width: 100%;
                padding: 12px;
            }
            
            .row {
                gap: 10px;
            }
            
            th, td {
                font-size: 11px;
                padding: 6px;
            }
            
            .badge {
                font-size: 9px;
            }
        }
        
        @media (max-width: 480px) {
            .animal-detail span {
                font-size: 10px;
                padding: 2px 6px;
            }
            
            th, td {
                font-size: 10px;
                padding: 4px;
            }
        }
    </style>
</head>
<body>

<!-- ФИКСИРОВАННАЯ НАВИГАЦИЯ -->
<div class="nav-fixed">
    <div class="container">
        <h1>🐾 Ветеринарная клиника</h1>
        <div class="nav">
            <a href="#animaux"> Животные</a>
            <a href="#visites"> Визиты</a>
            <a href="#filtrer"> Фильтр</a>
            <a href="#proprietaires"> Владельцы</a>
            <a href="form.php"> Добавить</a>
            <a href="ajouter_visite.php"> Визит</a>
        </div>
    </div>
</div>

<div class="container">
    <!-- ОТОБРАЖЕНИЕ СООБЩЕНИЙ СЕССИИ -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <!-- БЛОК: СПИСОК ЖИВОТНЫХ -->
    <div class="card" id="animaux">
        <h2> Список животных</h2>
        <div class="animaux-liste">
            <?php if(count($animaux) > 0): ?>
                <!-- ЦИКЛ ДЛЯ ОТОБРАЖЕНИЯ ВСЕХ ЖИВОТНЫХ -->
                <?php foreach($animaux as $animal): ?>
                <div class="animal-item">
                    <div class="animal-info">
                        <div class="animal-nom"><?= htmlspecialchars($animal['nom']) ?></div>
                        <div class="animal-detail">
                            <span><?= htmlspecialchars($animal['type']) ?></span>
                            <span><?= $animal['age'] ?> лет</span>
                            <span><?= htmlspecialchars($animal['couleur']) ?></span>
                            <span> <?= htmlspecialchars($animal['prenom'] . ' ' . $animal['proprietaire_nom']) ?></span>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <a href="form.php?modifier_id=<?= $animal['id'] ?>" class="btn btn-small btn-warning"> Изменить</a>
                        <a href="supprimer.php?id=<?= $animal['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Удалить <?= htmlspecialchars($animal['nom']) ?> ?')"> Удалить</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #999; text-align: center;"> Нет зарегистрированных животных</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- БЛОК: ЖУРНАЛ ВИЗИТОВ -->
    <div class="card" id="visites">
        <h2> Журнал визитов</h2>
        <?php if(count($visites) > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th> Дата</th><th> Животное</th><th> Симптомы</th><th> Болезнь</th><th> Лечение</th></tr>
                    </thead>
                    <tbody>
                        <!-- ЦИКЛ ДЛЯ ОТОБРАЖЕНИЯ ВСЕХ ВИЗИТОВ -->
                        <?php foreach($visites as $v): ?>
                        <tr>
                            <td><?= $v['date_visite'] ?></td>
                            <td><?= htmlspecialchars($v['animal_nom']) ?></td>
                            <td><?= htmlspecialchars($v['symptomes']) ?></td>
                            <td><?= htmlspecialchars($v['nom_maladie']) ?></td>
                            <td><?= htmlspecialchars($v['traitement']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: #999; text-align: center;"> Нет зарегистрированных визитов</p>
        <?php endif; ?>
    </div>

    <!-- БЛОК: ФИЛЬТРАЦИЯ ПО БОЛЕЗНИ -->
    <div class="card" id="filtrer">
        <h2>🔍 Фильтр визитов по болезни</h2>
        <form method="POST">
            <div class="row">
                <div class="col">
                    <select name="maladie_id" required style="width: 100%; padding: 10px;">
                        <option value="">-- Выберите болезнь --</option>
                        <?php foreach($maladies as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($maladie_selectionnee == $m['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nom_maladie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" name="filtrer">🔍 Показать</button>
                </div>
            </div>
        </form>

        <!-- ОТОБРАЖЕНИЕ РЕЗУЛЬТАТОВ ФИЛЬТРАЦИИ -->
        <?php if($visites_filtrees): ?>
            <h3 style="margin-top: 20px; font-size: 14px;"> Результаты (<?= count($visites_filtrees) ?> визит(ов))</h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th> Дата</th><th> Животное</th><th> Симптомы</th><th> Лечение</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($visites_filtrees as $vf): ?>
                        <tr>
                            <td><?= $vf['date_visite'] ?></td>
                            <td><?= htmlspecialchars($vf['animal_nom']) ?></td>
                            <td><?= htmlspecialchars($vf['symptomes']) ?></td>
                            <td><?= htmlspecialchars($vf['traitement']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p style="color: #c62828; margin-top: 15px;"> Визитов по этой болезни не найдено.</p>
        <?php endif; ?>
    </div>

    <!-- БЛОК: СПИСОК ВЛАДЕЛЬЦЕВ -->
    <div class="card" id="proprietaires">
        <h2> Список владельцев</h2>
        
        <?php if(count($proprietaires_list) > 0): ?>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th> Имя</th>
                            <th> Фамилия</th>
                            <th> Телефон</th>
                            <th> Email</th>
                            <th> Животных</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ЦИКЛ ДЛЯ ОТОБРАЖЕНИЯ ВСЕХ ВЛАДЕЛЬЦЕВ -->
                        <?php foreach($proprietaires_list as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['prenom']) ?></td>
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><?= htmlspecialchars($p['telephone']) ?></td>
                            <td><?= htmlspecialchars($p['email']) ?></td>
                            <td>
                                <?= $p['nombre_animaux'] ?>
                                <?php if($p['nombre_animaux'] > 0): ?>
                                    <span class="badge"></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: #999; text-align: center;">👥 Нет зарегистрированных владельцев</p>
        <?php endif; ?>
    </div>

    <!-- БЛОК: СПИСОК БОЛЕЗНЕЙ -->
    <div class="card">
        <h2> Список болезней</h2>
        <?php if(count($maladies) > 0): ?>
            <ul style="list-style: none; padding-left: 0;">
                <?php foreach($maladies as $m): ?>
                    <li style="padding: 8px 0; border-bottom: 1px solid #eee;">
                        <strong> <?= htmlspecialchars($m['nom_maladie']) ?></strong><br>
                        <span style="color: #666; font-size: 12px;"> <?= htmlspecialchars($m['description']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="color: #999; text-align: center;"> Нет зарегистрированных болезней</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
