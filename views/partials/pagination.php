<?php /** @var int $totalPages */ ?>
<?php /** @var int $currentPage */ ?>
<?php /** @var string $pageParam */ ?>

<?php
if (($totalPages ?? 1) <= 1) {
    return;
}
?>

<div class="pagination">
    <?php if ($currentPage <= 1): ?>
        <span class="disabled">←</span>
    <?php else: ?>
        <a href="<?= qs([$pageParam => $currentPage - 1]) ?>">←</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i === (int)$currentPage): ?>
            <span class="current"><?= $i ?></span>
        <?php else: ?>
            <a href="<?= qs([$pageParam => $i]) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($currentPage >= $totalPages): ?>
        <span class="disabled">→</span>
    <?php else: ?>
        <a href="<?= qs([$pageParam => $currentPage + 1]) ?>">→</a>
    <?php endif; ?>
</div>
