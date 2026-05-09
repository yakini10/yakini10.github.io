<?php
require_once 'config.php';

$id = $_GET['id'];

try {
    // Начинаем транзакцию
    $pdo->beginTransaction();
    
    // Сначала получаем ID владельца животного
    $stmt = $pdo->prepare("SELECT id_proprietaire FROM animaux WHERE id = ?");
    $stmt->execute([$id]);
    $animal = $stmt->fetch();
    
    if ($animal) {
        $id_proprietaire = $animal['id_proprietaire'];
        
        // Удаляем животное
        $stmt = $pdo->prepare("DELETE FROM animaux WHERE id = ?");
        $stmt->execute([$id]);
        
        // Проверяем, есть ли у владельца другие животные
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM animaux WHERE id_proprietaire = ?");
        $stmt->execute([$id_proprietaire]);
        $count = $stmt->fetch();
        
        // Если у владельца больше нет животных, удаляем его
        if ($count['total'] == 0) {
            $stmt = $pdo->prepare("DELETE FROM proprietaires WHERE id = ?");
            $stmt->execute([$id_proprietaire]);
            $_SESSION['message'] = " Животное и его владелец (у которого больше нет животных) успешно удалены!";
        } else {
            $_SESSION['message'] = " Животное успешно удалено! У владельца осталось " . $count['total'] . " животное(ых).";
        }
    } else {
        $_SESSION['message'] = " Животное не найдено!";
    }
    
    // Подтверждаем транзакцию
    $pdo->commit();
    
} catch(Exception $e) {
    // В случае ошибки отменяем транзакцию
    $pdo->rollBack();
    $_SESSION['message'] = " Ошибка при удалении: " . $e->getMessage();
}

header('Location: index.php');
exit();
?>
