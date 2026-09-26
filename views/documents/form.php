<?php /** @var string $formAction */ ?>
<?php /** @var mixed $allUsers */ ?>

<h1 class="page-title"><?= e($formTitle ?? 'Добавление документа') ?></h1>

<?php if (!empty($errors)): ?>
    <ul class="errors">
        <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="<?= e($formAction) ?>">
        <?php if (!empty($doc['id'])): ?>
            <input type="hidden" name="id" value="<?= e($doc['id']) ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Пользователь</label>
            <select name="user_id">
                <?php foreach ($allUsers as $u): ?>
                    <option value="<?= e($u['id']) ?>" <?= selected(old('user_id', $doc['user_id'] ?? ''), $u['id']) ?>>
                        <?= e($u['fio']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Наименование <span class="req">*</span></label>
            <input type="text" name="name" value="<?= old('name', $doc['name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Описание</label>
            <textarea name="description"><?= old('description', $doc['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Тип документа <span class="req">*</span></label>
            <select name="doc_type">
                <?php foreach (['Excel', 'Word', 'TXT'] as $type): ?>
                    <option value="<?= e($type) ?>" <?= selected(old('doc_type', $doc['doc_type'] ?? ''), $type) ?>>
                        <?= e($type) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn"><?= e($submitLabel ?? 'Добавить') ?></button>
        <a class="btn btn-secondary" href="index.php">Отмена</a>
    </form>
</div>
