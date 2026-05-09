<?php
require_once 'config.php';

// АВТОМАТИЧЕСКАЯ ОЧИСТКА : Удаление владельцев без животных
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ветеринарная клиника</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f8f0; padding: 20px; padding-top: 100px; }
        .container { max-width: 1300px; margin: 0 auto; }
        
        /* Navigation fixe */
        .nav-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #2e7d32;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            padding: 15px 20px;
        }
        
        .nav-fixed .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .nav-fixed h1 {
            color: white;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .nav-fixed .nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .nav-fixed .nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: #1b5e20;
            border-radius: 5px;
            transition: 0.3s;
        }
        
        .nav-fixed .nav a:hover {
            background: #0d3b0f;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 130px;
            }
            
            .nav-fixed .container {
                flex-direction: column;
                text-align: center;
            }
            
            .nav-fixed .nav {
                justify-content: center;
            }
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        .card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h2 { color: #2e7d32; margin-bottom: 15px; border-bottom: 2px solid #2e7d32; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        
        button, .btn { background: #2e7d32; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-danger { background: #c62828; }
        .btn-warning { background: #f57c00; }
        .btn-small { padding: 5px 10px; font-size: 12px; margin: 2px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2e7d32; color: white; }
        tr:hover { background: #f5f5f5; }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .message { background: #c8e6c9; padding: 10px; border-radius: 5px; margin-bottom: 20px; color: #1b5e20; border-left: 4px solid #2e7d32; }
        .row { display: flex; gap: 20px; flex-wrap: wrap; }
        .col { flex: 1; min-width: 250px; }
        
        /* AFFICHAGE ORIGINAL DES ANIMAUX (EN COLONNE) */
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
        
        .badge {
            display: inline-block;
            background: #2e7d32;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 5px;
        }
        
        @media (max-width: 768px) {
            .col { flex: 100%; }
            .animal-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .action-buttons {
                width: 100%;
                justify-content: flex-start;
            }
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        table {
            min-width: 500px;
        }
    </style>
</head>
<body>

<!-- NAVIGATION FIXE -->
<div class="nav-fixed">
    <div class="container">
        <h1>Ветеринарная клиника</h1>
        <div class="nav">
            <a href="#animaux">Животные</a>
            <a href="#visites">Визиты</a>
            <a href="#filtrer">Фильтр</a>
            <a href="#proprietaires">Владельцы</a>
            <a href="form.php">Добавить животное</a>
            <a href="ajouter_visite.php">Добавить визит</a>
        </div>
    </div>
</div>

<div class="container">
    <?php if(isset($_SESSION['message'])): ?>
        <div class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <!-- СПИСОК ЖИВОТНЫХ -->
    <div class="card" id="animaux">
        <h2>Список животных</h2>
        <div class="animaux-liste">
            <?php foreach($animaux as $animal): ?>
            <div class="animal-item">
                <div class="animal-info">
                    <div class="animal-nom"><?= htmlspecialchars($animal['nom']) ?></div>
                    <div class="animal-detail">
                        <span><?= htmlspecialchars($animal['type']) ?></span>
                        <span><?= $animal['age'] ?> лет</span>
                        <span><?= htmlspecialchars($animal['couleur']) ?></span>
                        <span><?= htmlspecialchars($animal['prenom'] . ' ' . $animal['proprietaire_nom']) ?></span>
                    </div>
                </div>
                <div class="action-buttons">
                    <a href="form.php?modifier_id=<?= $animal['id'] ?>" class="btn btn-small btn-warning">Изменить</a>
                    <a href="supprimer.php?id=<?= $animal['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Удалить <?= htmlspecialchars($animal['nom']) ?> ?')">Удалить</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ЖУРНАЛ ВИЗИТОВ -->
    <div class="card" id="visites">
        <h2>Журнал визитов</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Дата</th><th>Животное</th><th>Симптомы</th><th>Болезнь</th><th>Лечение</th></tr>
                </thead>
                <tbody>
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
    </div>

    <!-- ФИЛЬТРАЦИЯ ПО БОЛЕЗНИ -->
    <div class="card" id="filtrer">
        <h2>Фильтр визитов по болезни</h2>
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
                    <button type="submit" name="filtrer">Показать</button>
                </div>
            </div>
        </form>

        <?php if($visites_filtrees): ?>
            <h3 style="margin-top: 20px;">Результаты (<?= count($visites_filtrees) ?> визит(ов))</h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>Дата</th><th>Животное</th><th>Симптомы</th><th>Лечение</th></tr>
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
            <p style="color: #c62828; margin-top: 15px;">Визитов по этой болезни не найдено.</p>
        <?php endif; ?>
    </div>

    <!-- СПИСОК ВЛАДЕЛЬЦЕВ -->
    <div class="card" id="proprietaires">
        <h2>Список владельцев</h2>
        
        <?php if(count($proprietaires_list) > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Фамилия</th>
                            <th>Телефон</th>
                            <th>Email</th>
                            <th>Животных</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                    <span class="badge">✓</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: #999; text-align: center;">Нет зарегистрированных владельцев</p>
        <?php endif; ?>
    </div>

    <!-- СПИСОК БОЛЕЗНЕЙ -->
    <div class="card">
        <h2>Список болезней</h2>
        <ul>
            <?php foreach($maladies as $m): ?>
                <li><strong><?= htmlspecialchars($m['nom_maladie']) ?></strong> : <?= htmlspecialchars($m['description']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
</body>
</html>
