<?php /** @var string $formAction */ ?>
<?php /** @var string $login */ ?>
<?php /** @var array<int, string> $errors */ ?>

<h1 class="page-title"><?= e($formTitle ?? 'Авторизация') ?></h1>

<?php if (!empty($errors)): ?>
    <ul class="errors">
        <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="<?= e($formAction) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label>Логин <span class="req">*</span></label>
            <input type="text" name="login" value="<?= e($login ?? '') ?>" required autofocus>
        </div>

        <div class="form-group">
            <label>Пароль <span class="req">*</span></label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn"><?= e($submitLabel ?? 'Войти') ?></button>
    </form>
</div>