<?php
require_once 'config.php';

$id = $_GET['id'];

// Récupérer l'animal avec son propriétaire
$stmt = $pdo->prepare("
    SELECT a.*, p.prenom, p.nom as proprietaire_nom 
    FROM animaux a 
    LEFT JOIN proprietaires p ON a.id_proprietaire = p.id 
    WHERE a.id = ?
");
$stmt->execute([$id]);
$animal = $stmt->fetch();

if (!$animal) {
    $_SESSION['message'] = " Животное не найдено!";
    header('Location: index.php');
    exit();
}

$id_proprietaire = $animal['id_proprietaire'];
$nom_animal = $animal['nom'];

// Supprimer l'animal
$stmt = $pdo->prepare("DELETE FROM animaux WHERE id = ?");
$stmt->execute([$id]);

// Vérifier si le propriétaire existe encore et s'il a d'autres animaux
if ($id_proprietaire) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM animaux WHERE id_proprietaire = ?");
    $stmt->execute([$id_proprietaire]);
    $count = $stmt->fetch();
    
    // Si le propriétaire n'a plus d'animaux, le supprimer
    if ($count['total'] == 0) {
        $stmt = $pdo->prepare("DELETE FROM proprietaires WHERE id = ?");
        $stmt->execute([$id_proprietaire]);
        
        $_SESSION['message'] = " Животное '{$nom_animal}' удалено. Владелец '{$animal['prenom']} {$animal['proprietaire_nom']}' также автоматически удален (у него больше нет животных).";
    } else {
        $_SESSION['message'] = " Животное '{$nom_animal}' удалено! У владельца '{$animal['prenom']} {$animal['proprietaire_nom']}' осталось {$count['total']} животное(ых).";
    }
} else {
    $_SESSION['message'] = " Животное '{$nom_animal}' удалено! (У этого животного не было владельца)";
}

header('Location: index.php');
exit();
?>
