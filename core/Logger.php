<?php

class Logger
{
    private const LOG_FILE = __DIR__ . '/../logs/app.log';

    public static function log(string $level, string $message, string $context = ''): void
    {
        $dir = dirname(self::LOG_FILE);
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        $line = sprintf(
            "[%s] %s: %s%s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $context !== '' ? ' | ' . $context : ''
        );

        error_log($line, 3, self::LOG_FILE);
    }

    public static function error(string $message, string $context = ''): void
    {
        self::log('error', $message, $context);
    }

    public static function exception(Throwable $e): void
    {
        self::log('error', $e->getMessage(), $e->getFile() . ':' . $e->getLine());
    }

}