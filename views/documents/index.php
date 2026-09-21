<div class="section" id="documents">
    <h1 class="page-title">Документы</h1>

    <?php if (!empty($filteredUser)): ?>
        <div class="filter-banner">
            Показаны документы пользователя: <strong><?= e($filteredUser['fio']) ?></strong>
            &nbsp;·&nbsp; <a href="<?= qs(['user_id' => null, 'dpage' => null]) ?>">Показать все</a>
        </div>
    <?php endif; ?>

    <form class="search-form" method="get" action="index.php">
        <?php if (!empty($userId)): ?>
            <input type="hidden" name="user_id" value="<?= e($userId) ?>">
        <?php endif; ?>
        <input type="text" name="doc_search" placeholder="Поиск по наименованию" value="<?= e($docSearch ?? '') ?>">
        <button type="submit" class="btn">Найти</button>
        <?php if (!empty($docSearch)): ?>
            <a class="btn btn-secondary" href="<?= qs(['doc_search' => null, 'dpage' => null]) ?>">Сбросить</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Наименование</th>
            <th>Тип документа</th>
            <th>Дата добавления</th>
            <th>Дата редактирования</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($documents)): ?>
            <tr>
                <td colspan="6">Записей не найдено</td>
            </tr>
        <?php else: ?>
            <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= e($doc['id']) ?></td>
                    <td><?= e($doc['name']) ?></td>
                    <td><?= e($doc['doc_type']) ?></td>
                    <td><?= fmt_date($doc['created_at']) ?></td>
                    <td><?= fmt_date($doc['updated_at']) ?></td>
                    <td class="actions">
                        <a href="document_edit.php?id=<?= e($doc['id']) ?>">Редактировать</a>
                        <a class="danger" href="document_delete.php?id=<?= e($doc['id']) ?>"
                           onclick="return confirm('Удалить документ?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a class="add-link" href="document_create.php">Добавить</a>

    <?php
    $currentPage = $dpage ?? 1;
    $totalPages  = $docTotalPages ?? 1;
    $pageParam   = 'dpage';
    require __DIR__ . '/../partials/pagination.php';
    ?>
</div>
