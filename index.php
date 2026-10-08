<?php

declare(strict_types=1);

require __DIR__ . '/core/Autoloader.php';

$autoloader = new App\Core\Autoloader();
$autoloader
    ->addNamespace('App\Core', __DIR__ . '/core')
    ->addNamespace('App\Models', __DIR__ . '/models')
    ->addNamespace('App\Controllers', __DIR__ . '/controllers')
    ->addNamespace('App\Validators', __DIR__ . '/validators')
    ->register();

require __DIR__ . '/core/helpers.php';

App\Core\App::run();
