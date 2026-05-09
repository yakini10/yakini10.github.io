<?php
            foreach ($languages as $lang) {
                $stmt = $db->prepare(
                    "INSERT INTO application_languages(application_id, language_id)
                    VALUES (?, ?)"
                );

                $stmt->execute([
                    $_SESSION['application_id'],
                    (int)$lang
                ]);
            }

        else {

            $stmt = $db->prepare(
                "INSERT INTO application
                (fio, phone, email, birth_date, gender, biography, contract_accepted)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $fio,
                $phone,
                $email,
                $birth_date,
                $gender,
                $biography,
                1
            ]);

            $app_id = $db->lastInsertId();

            foreach ($languages as $lang) {

                $stmt = $db->prepare(
                    "INSERT INTO application_languages(application_id, language_id)
                    VALUES (?, ?)"
                );

                $stmt->execute([$app_id, (int)$lang]);
            }

            $login = 'user' . random_int(1000, 9999);
            $password = bin2hex(random_bytes(4));

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare(
                "INSERT INTO users(login, password_hash, application_id)
                VALUES (?, ?, ?)"
            );

            $stmt->execute([$login, $hash, $app_id]);
        }

        $db->commit();

        $messages[] = '<div class="success">Data saved successfully</div>';

    } catch (Exception $e) {

        $db->rollBack();
        $messages[] = '<div class="error">Database error</div>';
    }

    include('form.php');
}
?>
