<?php
require __DIR__ . '/core/init.php';

echo '<pre>';
$pass = 0;
$fail = 0;

function check(string $name, bool $ok): void
{
    global $pass, $fail;
    $ok ? $pass++ : $fail++;
    echo ($ok ? '✅ PASS' : '❌ FAIL') . " — $name\n";
}

// 0. подготовка: юзер-владелец

// уборка после упавших прошлых прогонов: тестовые юзеры docowner* (каскад удалит их документы)
$stale = Database::getConnection()
    ->query("SELECT id FROM users WHERE login LIKE 'docowner%'")
    ->fetchAll(PDO::FETCH_COLUMN);
foreach ($stale as $id) {
    User::delete((int)$id);
}

$uid = User::create([
    'fio' => 'Хозяин Документов', 'city' => 'Гор', 'phone' => '',
    'email' => 'docowner' . time() . '@t.ru', 'login' => 'docowner' . time(), 'password' => '123',
]);
$uid2 = User::create([
    'fio' => 'Второй Хозяин', 'city' => '', 'phone' => '',
    'email' => 'docowner2' . time() . '@t.ru', 'login' => 'docowner2' . time(), 'password' => '123',
]);

$start = Document::count('');

// 1. create
$d1 = Document::create(['user_id' => $uid, 'name' => 'Отчёт за сентябрь', 'description' => 'Финансы', 'doc_type' => 'Excel']);
$d2 = Document::create(['user_id' => $uid, 'name' => 'Договор аренды', 'description' => null, 'doc_type' => 'Word']);
$d3 = Document::create(['user_id' => $uid2, 'name' => 'Заметки', 'description' => 'Разное', 'doc_type' => 'TXT']);
check('create вернул id > 0 (3 шт)', $d1 > 0 && $d2 > 0 && $d3 > 0);

// 2. find
$doc = Document::find($d1);
check('find вернул документ', $doc !== null && $doc['name'] === 'Отчёт за сентябрь');
check('find(999999) = null', Document::find(999999) === null);

// 3. count
check('count вырос на 3', Document::count('') === $start + 3);
check('count по юзеру = 2', Document::count('', $uid) === 2);
check('count по поиску "Отчёт" = 1', Document::count('Отчёт') === 1);

// 4. all: JOIN и сортировка
$all = Document::all('', 1, 50);
$row1 = null;
foreach ($all as $r) { if ((int)$r['id'] === $d1) { $row1 = $r; break; } }
check('all вернул user_fio через JOIN', $row1 !== null && $row1['user_fio'] === 'Хозяин Документов');
check('all отсортирован по created_at DESC (свежие выше)',
    count($all) < 2 || strtotime($all[0]['created_at']) >= strtotime($all[1]['created_at']));

// 5. all: фильтр по юзеру
$only = Document::all('', 1, 50, $uid);
check('фильтр по юзеру: только его документы', count($only) === 2);
$hasForeign = false;
foreach ($only as $r) { if ((int)$r['user_id'] !== $uid) { $hasForeign = true; } }
check('в фильтре нет чужих документов', !$hasForeign);

// 6. all: поиск + фильтр вместе
$combo = Document::all('Договор', 1, 50, $uid);
check('поиск + фильтр вместе', count($combo) === 1 && (int)$combo[0]['id'] === $d2);

// 7. all: пагинация
$p1 = Document::all('', 1, 2);
$p2 = Document::all('', 2, 2);
$ids1 = array_map('intval', array_column($p1, 'id'));
$ids2 = array_map('intval', array_column($p2, 'id'));
$total = Document::count('');
check('страницы по 2 не пересекаются',
    count($ids1) === 2 &&
    count($ids1) + count($ids2) === $total &&
    empty(array_intersect($ids1, $ids2)));

// 8. update
Document::update($d1, ['user_id' => $uid2, 'name' => 'Отчёт ИЗМЕНЁН', 'description' => 'Новое', 'doc_type' => 'TXT']);
$after = Document::find($d1);
check('update изменил поля', $after['name'] === 'Отчёт ИЗМЕНЁН' && $after['doc_type'] === 'TXT' && (int)$after['user_id'] === $uid2);
check('update проставил updated_at', $after['updated_at'] !== null);

// 9. delete + каскад юзера
Document::delete($d2);
check('после delete find = null', Document::find($d2) === null);

User::delete($uid2);   // у него d1 (переведён) и d3
check('каскад: документы юзера удалены вместе с ним',
    Document::find($d1) === null && Document::find($d3) === null);
check('count вернулся', Document::count('') === $start);

// уборка
User::delete($uid);

echo "\n========================================\n";
echo "ИТОГ: $pass passed, $fail failed\n";
echo '</pre>';