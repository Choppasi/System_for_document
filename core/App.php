<?php

declare(strict_types=1);

class App
{
    public static function run(): void
    {
        $context = self::boot();

        $router = new Router();
        self::route($router, $context);
        $router->dispatch();
    }

    public static function boot(): array
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require __DIR__ . '/Logger.php';
        require __DIR__ . '/Database.php';
        require __DIR__ . '/View.php';
        require __DIR__ . '/Router.php';
        require __DIR__ . '/Request.php';
        require __DIR__ . '/helpers.php';
        require __DIR__ . '/../models/User.php';
        require __DIR__ . '/../models/Document.php';
        require __DIR__ . '/../validators/UserValidator.php';
        require __DIR__ . '/../validators/DocumentValidator.php';
        require __DIR__ . '/../controllers/HomeController.php';
        require __DIR__ . '/../controllers/UserController.php';
        require __DIR__ . '/../controllers/DocumentController.php';
        require __DIR__ . '/../controllers/AuthorizationController.php';
        require __DIR__ . '/../controllers/LogController.php';

        self::registerErrorHandlers();

        $pdo                = Database::getConnection();
        $request            = new Request();
        $userModel          = new User($pdo);
        $documentModel      = new Document($pdo);
        $userValidator      = new UserValidator($userModel);
        $documentValidator  = new DocumentValidator($userModel);
        $homeController     = new HomeController($userModel, $documentModel, $request);
        $userController     = new UserController($userModel, $request, $userValidator);
        $documentController = new DocumentController($documentModel, $userModel, $request, $documentValidator);
        $authController     = new AuthorizationController($userModel, $request);
        $logController      = new LogController();

        return compact(
            'pdo',
            'request',
            'userModel',
            'documentModel',
            'homeController',
            'userController',
            'documentController',
            'authController',
            'logController'
        );
    }

    public static function route(Router $router, array $context): void
    {
        $home = $context['homeController'];
        $user = $context['userController'];
        $doc  = $context['documentController'];
        $auth = $context['authController'];
        $log  = $context['logController'];

        $router->add(['GET', 'POST'], '/',                 [$home, 'index']);

        $router->add(['GET', 'POST'], '/users/create',     [$user, 'create']);
        $router->add(['GET', 'POST'], '/users/edit',       [$user, 'update']);
        $router->add(['GET', 'POST'], '/users/delete',     [$user, 'delete']);

        $router->add(['GET', 'POST'], '/documents/create', [$doc, 'create']);
        $router->add(['GET', 'POST'], '/documents/edit',   [$doc, 'update']);
        $router->add(['GET', 'POST'], '/documents/delete', [$doc, 'delete']);

        $router->add(['GET', 'POST'], '/login',            [$auth, 'login']);
        $router->add('GET',           '/logout',           [$auth, 'logout']);

        $router->add('GET',           '/logs',             [$log, 'index']);
    }

    private static function registerErrorHandlers(): void
    {
        set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): bool {
            if (!(error_reporting() & $errno)) {
                return false;
            }
            Logger::error($errstr, "$errfile:$errline");
            return true;
        });

        set_exception_handler(static function (Throwable $e): void {
            Logger::exception($e);

            http_response_code(500);

            echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Ошибка</title></head>'
                . '<body style="font-family:sans-serif; padding:40px;">'
                . '<h1>Что-то пошло не так</h1>'
                . '<p>Внутренняя ошибка сервера. Подробности записаны в storage/logs/app.log.</p>'
                . '<p style="color:#e74c3c;"><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>'
                . '<p style="color:#888;font-size:13px;">' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>'
                . '<p><a href="/">На главную</a></p>'
                . '</body></html>';
            exit;
        });

        register_shutdown_function(static function (): void {
            $error = error_get_last();
            if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
                Logger::log('fatal', $error['message'], $error['file'] . ':' . $error['line']);
            }
        });
    }
}
