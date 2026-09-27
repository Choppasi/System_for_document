<?php
// Delete user: GET ?id=... with JS confirm, deletes user (and cascades documents)
require __DIR__.'/core/init.php';

require_auth();

$id = (int)$_GET['id'];
if ($id > 0){
    User::delete($id);
}

redirect('index.php');