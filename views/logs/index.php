<?php /** @var mixed $logFile */ ?>
<?php /** @var array<int> $lines */ ?>
<div class="section">
    <h1 class="page-title">Логи ошибок</h1>

    <div class="log-toolbar">
        <span class="muted">Файл: <?= e($logFile) ?> · последние <?= count($lines) ?> записей</span>
        <a class="btn btn-secondary" href="/logs">Обновить</a>
    </div>

    <?php if (empty($lines)): ?>
        <p class="muted">Лог пуст — ошибок не зафиксировано.</p>
    <?php else: ?>
        <div class="log">
            <?php foreach ($lines as $line): ?>
                <?php
                $class = '';
                if (str_contains($line, 'FATAL')) {
                    $class = 'log-fatal';
                } elseif (str_contains($line, 'ERROR')) {
                    $class = 'log-error';
                } elseif (str_contains($line, 'WARNING')) {
                    $class = 'log-warning';
                }
                ?>
                <div class="log-line <?= $class ?>"><?= e($line) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
