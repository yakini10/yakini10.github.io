<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login</title>
</head>
<body>

<h1>Login</h1>

<form action="index.php" method="POST">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
<input type="hidden" name="auth" value="1">

<label>Login</label>
<input type="text" name="login">

<br><br>

<label>Password</label>
<input type="password" name="password">

<br><br>
