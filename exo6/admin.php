<td><?= htmlspecialchars($app['email']) ?></td>
                                <td><?= htmlspecialchars($app['phone']) ?></td>
                                <td><?= htmlspecialchars($app['birth_date']) ?></td>
                                <td><?= $app['gender'] == 'male' ? 'Мужской' : 'Женский' ?></td>
                                <td><?= htmlspecialchars($app['login'] ?? '—') ?></td>
                                <td>
                                    <a href="admin.php?action=edit&id=<?= $app['id'] ?>" class="btn btn-edit"> Редактировать</a>
                                    <a href="admin.php?action=delete&id=<?= $app['id'] ?>" class="btn btn-delete" onclick="return confirm('Удалить эту заявку навсегда?')"> Удалить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="footer-note">
            Всего: <?= count($applications) ?> заявка(и) Вы вошли как администратор
        </div>
    <?php endif; ?>
</div>
</body>
</html>
