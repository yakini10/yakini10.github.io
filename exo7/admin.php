<?php
            ->execute([$id]);

        $db->commit();

    } catch (Exception $e) {
        $db->rollBack();
    }
}

$applications = $db->query(
    "SELECT a.*, u.login
     FROM application a
     LEFT JOIN users u ON a.id = u.application_id
     ORDER BY a.id DESC"
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Admin</title>
</head>
<body>

<h1>Administration</h1>

<table border="1">
<tr>
<th>ID</th>
<th>FIO</th>
<th>Email</th>
<th>Actions</th>
</tr>

<?php foreach ($applications as $app): ?>
<tr>
<td><?= (int)$app['id'] ?></td>
<td><?= htmlspecialchars($app['fio'], ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars($app['email'], ENT_QUOTES, 'UTF-8') ?></td>
<td>

<form method="POST">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= (int)$app['id'] ?>">
<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
<button type="submit">Delete</button>
</form>

</td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
