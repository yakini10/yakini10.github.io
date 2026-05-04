<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета</title>
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
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .auth-header {
            text-align: right;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .auth-status {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .logout-btn {
            background: #f44336;
            color: white;
            text-decoration: none;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        .admin-link {
            background: #ff9800;
            color: white;
            text-decoration: none;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .admin-link:hover {
            background: #f57c00;
            transform: translateY(-2px);
        }

        .form-group {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-group.error-group {
            background-color: #fff0f0;
            border-left: 4px solid #f44336;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        label.required::after {
            content: " *";
            color: #f44336;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input.error,
        textarea.error,
        select.error {
            border-color: #f44336;
            background-color: #fff0f0;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 5px;
            flex-wrap: wrap;
        }

        .radio-group label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: normal;
            cursor: pointer;
        }

        .radio-group input[type="radio"] {
            width: auto;
            cursor: pointer;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            cursor: pointer;
        }

        .checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
        }

        .error-message {
            color: #f44336;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .success {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        .error {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        select[multiple] {
            min-height: 150px;
        }

        select[multiple] option {
            padding: 8px;
            cursor: pointer;
        }

        select[multiple] option:hover {
            background: #667eea20;
        }

        small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #888;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
            
            .auth-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="auth-header">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <span class="auth-status"> Вы вошли</span>
            <div style="display: flex; gap: 10px;">
                <a href="admin.php" class="admin-link"> Администрирование</a>
                <a href="index.php?logout=1" class="logout-btn"> Выйти</a>
            </div>
        <?php else: ?>
            <div style="display: flex; gap: 10px; width: 100%; justify-content: flex-end;">
                <a href="admin.php" class="admin-link"> Администрирование</a>
                <a href="login.php" class="logout-btn" style="background: #667eea;"> Войти</a>
            </div>
        <?php endif; ?>
    </div>

    <h1> Анкета</h1>

    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $message): ?>
            <?php echo $message; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <div class="form-group <?php echo isset($errors['fio']) ? 'error-group' : ''; ?>">
            <label for="fio" class="required">ФИО</label>
            <input type="text" id="fio" name="fio" value="<?php echo htmlspecialchars($values['fio'] ?? ''); ?>" placeholder="Иванов Иван Иванович" class="<?php echo isset($errors['fio']) ? 'error' : ''; ?>">
        </div>

        <div class="form-group <?php echo isset($errors['phone']) ? 'error-group' : ''; ?>">
            <label for="phone" class="required">Телефон</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>" placeholder="+7 XXX XXX XX XX" class="<?php echo isset($errors['phone']) ? 'error' : ''; ?>">
        </div>

        <div class="form-group <?php echo isset($errors['email']) ? 'error-group' : ''; ?>">
            <label for="email" class="required">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>" placeholder="example@domain.com" class="<?php echo isset($errors['email']) ? 'error' : ''; ?>">
        </div>

        <div class="form-group <?php echo isset($errors['birth_date']) ? 'error-group' : ''; ?>">
            <label for="birth_date" class="required">Дата рождения</label>
            <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($values['birth_date'] ?? ''); ?>" class="<?php echo isset($errors['birth_date']) ? 'error' : ''; ?>">
        </div>

        <div class="form-group <?php echo isset($errors['gender']) ? 'error-group' : ''; ?>">
            <label class="required">Пол</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="male" <?php echo (($values['gender'] ?? '') == 'male') ? 'checked' : ''; ?>> Мужской</label>
                <label><input type="radio" name="gender" value="female" <?php echo (($values['gender'] ?? '') == 'female') ? 'checked' : ''; ?>> Женский</label>
            </div>
        </div>

        <div class="form-group <?php echo isset($errors['languages']) ? 'error-group' : ''; ?>">
            <label for="languages" class="required">Предпочитаемый(е) язык(и) программирования</label>
            <select name="languages[]" id="languages" multiple size="6" class="<?php echo isset($errors['languages']) ? 'error' : ''; ?>">
                <?php
                $langs = [
                    1=>'Pascal', 2=>'C', 3=>'C++', 4=>'JavaScript', 
                    5=>'PHP', 6=>'Python', 7=>'Java', 8=>'Haskell',
                    9=>'Clojure', 10=>'Prolog', 11=>'Scala', 12=>'Go'
                ];
                foreach($langs as $id=>$name){
                    $sel = in_array($id, $values['languages'] ?? []) ? 'selected' : '';
                    echo "<option value='$id' $sel>$name</option>";
                }
                ?>
            </select>
            <small>Удерживайте Ctrl (или Cmd) для выбора нескольких вариантов</small>
        </div>

        <div class="form-group <?php echo isset($errors['biography']) ? 'error-group' : ''; ?>">
            <label for="biography" class="required">Биография</label>
            <textarea id="biography" name="biography" rows="5" placeholder="Расскажите немного о себе..." class="<?php echo isset($errors['biography']) ? 'error' : ''; ?>"><?php echo htmlspecialchars($values['biography'] ?? ''); ?></textarea>
        </div>

        <div class="form-group <?php echo isset($errors['contract_accepted']) ? 'error-group' : ''; ?>">
            <div class="checkbox-group">
                <input type="checkbox" id="contract_accepted" name="contract_accepted" value="yes" <?php echo ($values['contract_accepted'] ?? false) ? 'checked' : ''; ?>>
                <label for="contract_accepted" class="required">Я принимаю условия договора</label>
            </div>
        </div>

        <button type="submit"> Сохранить</button>
    </form>
</div>
</body>
</html>
