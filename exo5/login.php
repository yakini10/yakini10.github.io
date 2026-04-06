<?php
session_start();

// Если уже авторизован, перенаправляем на форму
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$messages = [];

// Обработка сообщений об ошибках из index.php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['auth'])) {
    // Сообщения об ошибках будут переданы через сессию или обработаны в index.php
    // Этот блок в основном для отображения ошибок, переданных из index.php
}

// Получаем сообщения из cookies (если есть)
if (!empty($_COOKIE['login_error'])) {
    $messages[] = htmlspecialchars($_COOKIE['login_error']);
    setcookie('login_error', '', time() - 3600);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 400px;
            max-width: 100%;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        button:active {
            transform: translateY(0);
        }

        .error {
            background-color: #f44336;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .info {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }

        .info a {
            color: #667eea;
            text-decoration: none;
        }

        .info a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Вход в систему</h1>

        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="error"><?php echo $msg; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" placeholder="Введите ваш логин" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" placeholder="Введите ваш пароль" required>
            </div>
            
            <input type="hidden" name="auth" value="1">
            
            <button type="submit">Войти</button>
        </form>
        
        <div class="info">
            <p>После регистрации вы получите логин и пароль.<br>
            <a href="index.php">Вернуться к форме регистрации</a></p>
        </div>
    </div>
</body>
</html>
