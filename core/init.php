<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require __DIR__ . '/../config/config.php';
require __DIR__ . '/Logger.php';
require __DIR__ . '/Database.php';
require __DIR__ . '/View.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/../models/User.php';
require __DIR__ . '/../models/Document.php';
require __DIR__ . '/../controllers/UserController.php';
require __DIR__ . '/../controllers/DocumentController.php';


set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if(!(error_reporting() & $errno)){
        return false;
    }
    Logger::log('warning', $errstr, $errfile . ':' . $errline);
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

set_exception_handler(static function (Throwable $e): void {
    Logger::exception($e);

    http_response_code(500);

    echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Ошибка</title></head>'
        . '<body style="font-family:sans-serif; padding:40px;">'
        . '<h1>Что-то пошло не так</h1>'
        . '<p>Внутренняя ошибка сервера. Подробности записаны в logs/app.log.</p>'
        . '<p style="color:#e74c3c;"><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>'
        . '<p style="color:#888;font-size:13px;">' . htmlspecialchars($e->getFile()) . ':' .$e->getLine() . '</p>'
        . '<p><a href="/index.php">На главную</a></p>'
        . '</body></html>';
    exit;
});

register_shutdown_function(static function (): void {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)){
        Logger::log('fatal', $error['message'], $error['file'] . ':' . $error['line']);
    };
});

// --- Сборка зависимостей (DI-контейнер) ---
$pdo                = Database::getConnection();
$userModel          = new User($pdo);
$documentModel      = new Document($pdo);
$userController     = new UserController($userModel);
$documentController = new DocumentController($documentModel, $userModel);



