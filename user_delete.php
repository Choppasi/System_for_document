<?php

require __DIR__.'/core/init.php';

require_auth();

$id = (int)$_GET['id'];
if ($id > 0){
    $userModel->delete($id);
}

redirect('index.php');