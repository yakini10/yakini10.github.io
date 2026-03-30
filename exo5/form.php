<div class="container">
    <h1>Анкета</h1>

    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $message): ?>
            <?php echo $message; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <!-- ФИО -->
        <div class="form-group <?php echo isset($errors['fio']) ? 'error-group' : ''; ?>">
            <label for="fio" class="required">ФИО</label>
            <input type="text" id="fio" name="fio" value="<?php echo htmlspecialchars($values['fio'] ?? ''); ?>" class="<?php echo isset($errors['fio']) ? 'error' : ''; ?>">
        </div>

        <!-- Телефон -->
        <div class="form-group <?php echo isset($errors['phone']) ? 'error-group' : ''; ?>">
            <label for="phone" class="required">Телефон</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>" placeholder="+7 (XXX) XXX-XX-XX" class="<?php echo isset($errors['phone']) ? 'error' : ''; ?>">
        </div>

        <!-- Email -->
        <div class="form-group <?php echo isset($errors['email']) ? 'error-group' : ''; ?>">
            <label for="email" class="required">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>" placeholder="example@domain.com" class="<?php echo isset($errors['email']) ? 'error' : ''; ?>">
        </div>

        <!-- Дата рождения -->
        <div class="form-group <?php echo isset($errors['birth_date']) ? 'error-group' : ''; ?>">
            <label for="birth_date" class="required">Дата рождения</label>
            <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($values['birth_date'] ?? ''); ?>" class="<?php echo isset($errors['birth_date']) ? 'error' : ''; ?>">
        </div>

        <!-- Пол -->
        <div class="form-group <?php echo isset($errors['gender']) ? 'error-group' : ''; ?>">
            <label class="required">Пол</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="male" <?php echo (($values['gender'] ?? '') == 'male') ? 'checked' : ''; ?>> Мужской</label>
                <label><input type="radio" name="gender" value="female" <?php echo (($values['gender'] ?? '') == 'female') ? 'checked' : ''; ?>> Женский</label>
            </div>
        </div>

        <!-- Языки -->
        <div class="form-group <?php echo isset($errors['languages']) ? 'error-group' : ''; ?>">
            <label for="languages" class="required">Любимый язык программирования</label>
            <select name="languages[]" id="languages" multiple size="6" class="<?php echo isset($errors['languages']) ? 'error' : ''; ?>">
                <?php
                $langs = [
                    1=>'Pascal',2=>'C',3=>'C++',4=>'JavaScript',5=>'PHP',6=>'Python',
                    7=>'Java',8=>'Haskel',9=>'Clojure',10=>'Prolog',11=>'Scala',12=>'Go'
                ];
                foreach($langs as $id=>$name){
                    $sel = in_array($id, $values['languages'] ?? []) ? 'selected' : '';
                    echo "<option value='$id' $sel>$name</option>";
                }
                ?>
            </select>
            <small style="color: #666;">Удерживайте Ctrl (или Cmd) для выбора нескольких вариантов</small>
        </div>

        <!-- Биография -->
        <div class="form-group <?php echo isset($errors['biography']) ? 'error-group' : ''; ?>">
            <label for="biography" class="required">Биография</label>
            <textarea id="biography" name="biography" rows="5" class="<?php echo isset($errors['biography']) ? 'error' : ''; ?>"><?php echo htmlspecialchars($values['biography'] ?? ''); ?></textarea>
        </div>

        <!-- Контракт -->
        <div class="form-group <?php echo isset($errors['contract_accepted']) ? 'error-group' : ''; ?>">
            <div class="checkbox-group">
                <input type="checkbox" id="contract_accepted" name="contract_accepted" value="yes" <?php echo ($values['contract_accepted'] ?? false) ? 'checked' : ''; ?>>
                <label for="contract_accepted" class="required">С контрактом ознакомлен(а)</label>
            </div>
        </div>

        <button type="submit">Сохранить</button>
    </form>
</div>
