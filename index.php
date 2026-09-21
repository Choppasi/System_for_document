<?php
require __DIR__ . '/core/init.php';

// --- параметры юзеров ---
$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));

// --- параметры документов ---
$docSearch = trim($_GET['doc_search'] ?? '');
$dpage     = max(1, (int)($_GET['dpage'] ?? 1));
$userId    = (int)($_GET['user_id'] ?? 0);

// --- данные юзеров ---
$total      = User::count($search);
$totalPages = max(1, (int) ceil($total / 5));
$page       = min($page, $totalPages);          // защита: ?page=99 при 2 страницах → последняя
$users      = User::all($search, $page, 5);

// --- данные документов ---
$docTotal      = Document::count($docSearch, $userId);
$docTotalPages = max(1, (int) ceil($docTotal / 5));
$dpage         = min($dpage, $docTotalPages);
$documents     = Document::all($docSearch, $dpage, 5, $userId);

// --- ФИО владельца для плашки фильтра ---
$filteredUser = $userId > 0 ? User::find($userId) : null;

require __DIR__ . '/views/layouts/header.php';
require __DIR__ . '/views/users/index.php';
require __DIR__ . '/views/documents/index.php';
require __DIR__ . '/views/layouts/footer.php';
