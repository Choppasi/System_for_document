<?php
require __DIR__ . '/core/init.php';



$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));


$docSearch = trim($_GET['doc_search'] ?? '');
$dpage     = max(1, (int)($_GET['dpage'] ?? 1));
$userId    = (int)($_GET['user_id'] ?? 0);

$total      = User::count($search);
$totalPages = max(1, (int) ceil($total / 5));
$page       = min($page, $totalPages); 
$users      = User::all($search, $page, 5);


$docTotal      = Document::count($docSearch, $userId);
$docTotalPages = max(1, (int) ceil($docTotal / 5));
$dpage         = min($dpage, $docTotalPages);
$documents     = Document::all($docSearch, $dpage, 5, $userId);


$filteredUser = $userId > 0 ? User::find($userId) : null;

require __DIR__ . '/views/layouts/header.php';
require __DIR__ . '/views/users/index.php';
require __DIR__ . '/views/documents/index.php';
require __DIR__ . '/views/layouts/footer.php';
