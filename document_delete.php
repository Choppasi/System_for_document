<?php

require __DIR__ . '/core/init.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0 ){
    $documentModel->delete($id);
}

redirect('index.php');


