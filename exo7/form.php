<!DOCTYPE html>

<form action="index.php" method="POST">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

<label>FIO</label>
<input type="text" name="fio"
value="<?= htmlspecialchars($values['fio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<br><br>

<label>Phone</label>
<input type="text" name="phone"
value="<?= htmlspecialchars($values['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<br><br>

<label>Email</label>
<input type="email" name="email"
value="<?= htmlspecialchars($values['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<br><br>

<label>Birth date</label>
<input type="date" name="birth_date"
value="<?= htmlspecialchars($values['birth_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<br><br>

<label>Gender</label>
<select name="gender">
<option value="male">Male</option>
<option value="female">Female</option>
</select>

<br><br>

<label>Languages</label>
<select multiple name="languages[]">
<?php
foreach(getLanguagesList() as $id => $lang):
?>
<option value="<?= $id ?>">
<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>
</option>
<?php endforeach; ?>
</select>

<br><br>

<label>Biography</label>
<textarea name="biography">
<?= htmlspecialchars($values['biography'] ?? '', ENT_QUOTES, 'UTF-8') ?>
</textarea>

<br><br>

<label>
<input type="checkbox" name="contract_accepted">
Accept contract
</label>

<br><br>

<button type="submit">Save</button>

</form>

</body>
</html>
