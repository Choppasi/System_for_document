<?php

require __DIR__ . '/core/init.php';

require_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0 ){
    Document::delete($id);
}

redirect('index.php');


