<h1 class="page-title"><?= e($formTitle ?? 'Добавление пользователя') ?></h1>

<?php if (!empty($errors)): ?>
    <ul class="errors">
        <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="<?= e($formAction) ?>">
        <?php if (!empty($user['id'])): ?>
            <input type="hidden" name="id" value="<?= e($user['id']) ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Контактное лицо (ФИО) <span class="req">*</span></label>
            <input type="text" name="fio" value="<?= old('fio', $user['fio'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Город</label>
            <input type="text" name="city" value="<?= old('city', $user['city'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Телефон</label>
            <input type="text" name="phone" value="<?= old('phone', $user['phone'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>E-mail <span class="req">*</span></label>
            <input type="email" name="email" value="<?= old('email', $user['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Логин <span class="req">*</span></label>
            <input type="text" name="login" value="<?= old('login', $user['login'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Пароль <span class="req">*</span></label>
            <input type="password" name="password" value="<?= old('password', $user['password'] ?? '') ?>">
        </div>
        <button type="submit" class="btn"><?= e($submitLabel ?? 'Добавить') ?></button>
        <a class="btn btn-secondary" href="index.php">Отмена</a>
        
    </form>
</div>



