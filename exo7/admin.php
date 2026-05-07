<?php
session_start();
require_once 'config.php';

$db = getDB();

// CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// AUTH BASIC (inchangé)
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin panel"');
    exit('Authentication required');
}

$stmt = $db->prepare("SELECT * FROM admin WHERE login = ?");
$stmt->execute([$_SERVER['PHP_AUTH_USER']]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($_SERVER['PHP_AUTH_PW'], $admin['password_hash'])) {
    exit('Invalid credentials');
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

// DELETE sécurisé POST + CSRF
if ($action === 'delete' && isset($_POST['id'])) {

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die('CSRF blocked');
    }

    $id = (int)$_POST['id'];

    $db->beginTransaction();
    $db->prepare("DELETE FROM application_languages WHERE application_id = ?")->execute([$id]);
    $db->prepare("DELETE FROM users WHERE application_id = ?")->execute([$id]);
    $db->prepare("DELETE FROM application WHERE id = ?")->execute([$id]);
    $db->commit();
}
?>
