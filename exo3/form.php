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
    <option value="Pascal">Pascal</option>
    <option value="C">C</option>
    <option value="C++">C++</option>
    <option value="JavaScript">JavaScript</option>
    <option value="PHP">PHP</option>
    <option value="Python">Python</option>
    <option value="Java">Java</option>
    <option value="Haskel">Haskel</option>
    <option value="Clojure">Clojure</option>
    <option value="Prolog">Prolog</option>
    <option value="Scala">Scala</option>
    <option value="Go">Go</option>
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
