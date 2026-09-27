<?php

require __DIR__ . '/core/init.php';

require_auth();

DocumentController::update();
