<?php
require __DIR__ . '/core/init.php';

$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));

$users      = User::all($search, $page, 5);
$total      = User::count($search);
$totalPages = (int) ceil($total / 5);

require __DIR__ . '/views/layouts/header.php';
require __DIR__ . '/views/users/index.php';
require __DIR__ . '/views/layouts/footer.php';