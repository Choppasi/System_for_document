<div class="section" id="users">
    <h1 class="page-title">Пользователи</h1>

    <form class="search-form" method="get" action="/">
        <input type="text" name="search" placeholder="Поиск по ФИО" value="<?= e($search ?? '') ?>">
        <button type="submit" class="btn">Найти</button>
        <?php if (!empty($search)): ?>
            <a class="btn btn-secondary" href="/">Сбросить</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Контактное лицо (ФИО)</th>
            <th>Город</th>
            <th>Телефон</th>
            <th>E-mail</th>
            <th>Дата добавления</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr>
                <td colspan="7">Записей не найдено</td>
            </tr>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['id']) ?></td>
                    <td><?= e($user['fio']) ?></td>
                    <td><?= e($user['city']) ?></td>
                    <td><?= e($user['phone']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= fmt_date($user['created_at']) ?></td>
                    <td class="actions">
                        <a href="/users/edit?id=<?= e($user['id']) ?>">Редактировать</a>
                        <a class="danger" href="/users/delete?id=<?= e($user['id']) ?>"
                           onclick="return confirm('Удалить пользователя? Его документы также будут удалены.')">Удалить</a>
                        <a href="<?= qs(['user_id' => $user['id'], 'dpage' => null]) ?>">Документы</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a class="add-link" href="/users/create">Добавить</a>

    <?php
    $currentPage = $page ?? 1;
    $totalPages  = $totalPages ?? 1;
    $pageParam   = 'page';
    require __DIR__ . '/../partials/pagination.php';
    ?>
</div>
