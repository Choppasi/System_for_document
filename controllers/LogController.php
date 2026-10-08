<?php

namespace App\Controllers;

use App\Core\View;

class LogController
{
    private const LOG_FILE  = __DIR__ . '/../storage/logs/app.log';
    private const MAX_LINES = 300;

    public function index(): void
    {
        require_auth();

        View::render('logs/index', [
            'formTitle' => 'Логи ошибок',
            'logFile'   => 'storage/logs/app.log',
            'lines'     => $this->tail(self::LOG_FILE, self::MAX_LINES),
        ]);
    }

    private function tail(string $file, int $limit): array
    {
        if (!is_file($file) || !is_readable($file)) {
            return [];
        }

        $handle = fopen($file, 'rb');
        if ($handle === false) {
            return [];
        }

        $lines = [];
        while (($line = fgets($handle)) !== false) {
            $lines[] = rtrim($line, "\r\n");
            if (count($lines) > $limit) {
                array_shift($lines);
            }
        }

        fclose($handle);

        return $lines;
    }
}
