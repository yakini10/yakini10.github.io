<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Анкета</title>
</head>
<body>

<form action="" method="POST">
  
  <!-- 1) ФИО -->
  <label>ФИО:</label><br>
  <input type="text" name="fio" required><br><br>

  <!-- 2) Телефон -->
  <label>Телефон:</label><br>
  <input type="tel" name="phone" required><br><br>

  <!-- 3) Email -->
  <label>E-mail:</label><br>
  <input type="email" name="email" required><br><br>

  <!-- 4) Дата рождения -->
  <label>Дата рождения:</label><br>
  <input type="date" name="birth_date" required><br><br>

  <!-- 5) Пол -->
  <label>Пол:</label><br>
  <input type="radio" name="gender" value="male" required> Мужской<br>
  <input type="radio" name="gender" value="female"> Женский<br><br>

  <!-- 6) Любимый язык программирования -->
  <label>Любимый язык программирования:</label><br>
  <select name="languages[]" multiple size="6" required>
    <option value="1">Pascal">Pascal</option>
    <option value="2">C</option>
    <option value="3">C++</option>
    <option value="4">JavaScript</option>
    <option value="5">PHP</option>
    <option value="6">Python</option>
    <option value="7">Java</option>
    <option value="8">Haskel</option>
    <option value="9">Clojure</option>
    <option value="10">Prolog</option>
    <option value="11">Scala</option>
    <option value="12">Go</option>
  </select><br><br>

  <!-- 7) Биография -->
  <label>Биография:</label><br>
  <textarea name="biography" rows="5" cols="40" required></textarea><br><br>

  <!-- 8) С контрактом ознакомлен(а) -->
  <input type="checkbox" name="contract_accepted" value="yes" required>
  С контрактом ознакомлен(а)<br><br>

  <!-- 9) Кнопка сохранить -->
  <input type="submit" value="Сохранить">

</form>

</body>
</html>
