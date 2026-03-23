<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'application</title>
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
            padding: 10px 15px;
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
            background-color: #4caf50;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error {
            background-color: #f44336;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
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

        select[multiple] {
            min-height: 150px;
        }

        select[multiple] option {
            padding: 8px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Formulaire de candidature</h1>
        
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <?php echo $message; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <form action="" method="POST">
            <!-- Nom complet -->
            <div class="form-group <?php echo isset($errors['fio']) ? 'error-group' : ''; ?>">
                <label for="fio" class="required">Nom complet</label>
                <input type="text" 
                       id="fio" 
                       name="fio" 
                       value="<?php echo htmlspecialchars($values['fio'] ?? ''); ?>"
                       class="<?php echo isset($errors['fio']) ? 'error' : ''; ?>">
                <?php if (isset($error_messages['fio'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_messages['fio']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Téléphone -->
            <div class="form-group <?php echo isset($errors['phone']) ? 'error-group' : ''; ?>">
                <label for="phone" class="required">Téléphone</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>"
                       placeholder="+7 (XXX) XXX-XX-XX"
                       class="<?php echo isset($errors['phone']) ? 'error' : ''; ?>">
                <?php if (isset($error_messages['phone'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_messages['phone']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group <?php echo isset($errors['email']) ? 'error-group' : ''; ?>">
                <label for="email" class="required">E-mail</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>"
                       placeholder="exemple@domaine.com"
                       class="<?php echo isset($errors['email']) ? 'error' : ''; ?>">
                <?php if (isset($error_messages['email'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_messages['email']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Date de naissance -->
            <div class="form-group <?php echo isset($errors['birth_date']) ? 'error-group' : ''; ?>">
                <label for="birth_date" class="required">Date de naissance</label>
                <input type="date" 
                       id="birth_date" 
                       name="birth_date" 
                       value="<?php echo htmlspecialchars($values['birth_date'] ?? ''); ?>"
                       class="<?php echo isset($errors['birth_date']) ? 'error' : ''; ?>">
                <?php if (isset($error_messages['birth_date'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_messages['birth_date']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Sexe -->
            <div class="form-group <?php echo isset($errors['gender']) ? 'error-group' : ''; ?>">
                <label class="required">Sexe</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" 
                               name="gender" 
                               value="male" 
                               <?php echo (($values['gender'] ?? '') == 'male') ? 'checked' : ''; ?>>
                        Masculin
                    </label>
                    <label>
                        <input type="radio" 
                               name="gender" 
                               value="female" 
                               <?php echo (($values['gender'] ?? '') == 'female') ? 'checked' : ''; ?>>
                        Féminin
                    </label>
                </div>
                <?php if (isset($error_messages['gender'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_messages['gender']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Langages de programmation -->
            <div class="form-group <?php echo isset($errors['languages']) ? 'error-group' : ''; ?>">
                <label for="languages" class="required">Langages de programmation préférés</label>
                <select name="languages[]" 
                        id="languages" 
                        multiple 
                        size="6"
                        class="<?php echo isset($errors['languages']) ? 'error' : ''; ?>">
                    <option value="1" <?php echo in_array('1', $values['languages'] ?? []) ? 'selected' : ''; ?>>Pascal</option>
                    <option value="2" <?php echo in_array('2', $values['languages'] ?? []) ? 'selected' : ''; ?>>C</option>
                    <option value="3" <?php echo in_array('3', $values['languages'] ?? []) ? 'selected' : ''; ?>>C++</option>
                    <option value="4" <?php echo in_array('4', $values['languages'] ?? []) ? 'selected' : ''; ?>>JavaScript</option>
                    <option value="5" <?php echo in_array('5', $values['languages'] ?? []) ? 'selected' : ''; ?>>PHP</option>
                    <option value="6" <?php echo in_array('6', $values['languages'] ?? []) ? 'selected' : ''; ?>>Python</option>
                    <option value="7" <?php echo in_array('7', $values['languages'] ?? []) ? 'selected' : ''; ?>>Java</option>
                    <option value="8" <?php echo in_array('8', $values['languages'] ?? []) ? 'selected' : ''; ?>>Haskell</option>
                    <option value="9" <?php echo in_array('9', $values['languages'] ?? []) ? 'selected' : ''; ?>>Clojure</option>
                    <option value="10" <?php echo in_array('10', $values['languages'] ?? []) ? 'selected' : ''; ?>>Prolog</option>
                    <option value="11" <?php echo in_array('11', $values['languages'] ?? []) ? 'selected' : ''; ?>>Scala</option>
                    <option value="12" <?php echo in_array('12', $values['languages'] ?? []) ? 'selected' : ''; ?>>Go</option>
                </select>
                <small style="color: #666;">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs options</small>
                <?php if (isset($error_messages['languages'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_messages['languages']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Biographie -->
            <div class="form-group <?php echo isset($errors['biography']) ? 'error-group' : ''; ?>">
                <label for="biography" class="required">Biographie</label>
                <textarea id="biography" 
                          name="biography" 
                          rows="5"
                          class="<?php echo isset($errors['biography']) ? 'error' : ''; ?>"><?php echo htmlspecialchars($values['biography'] ?? ''); ?></textarea>
                <?php if (isset($error_messages['biography'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_messages['biography']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Contrat -->
            <div class="form-group <?php echo isset($errors['contract_accepted']) ? 'error-group' : ''; ?>">
                <div class="checkbox-group">
                    <input type="checkbox" 
                           id="contract_accepted" 
                           name="contract_accepted" 
                           value="yes"
                           <?php echo ($values['contract_accepted'] ?? false) ? 'checked' : ''; ?>>
                    <label for="contract_accepted" class="required">J'accepte les termes du contrat</label>
                </div>
                <?php if (isset($error_messages['contract_accepted'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_messages['contract_accepted']); ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Envoyer</button>
        </form>
    </div>
</body>
</html>
