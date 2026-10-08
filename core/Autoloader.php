<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Простой автозагрузчик классов в стиле PSR-4.
 *
 * Сопоставляет префикс namespace с базовой директорией и подключает
 * файл по имени класса при первом обращении к нему.
 */
final class Autoloader
{
    /** @var array<string, string> префикс namespace => базовая директория */
    private array $prefixes = [];

    public function addNamespace(string $prefix, string $baseDir): self
    {
        $prefix  = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR;

        $this->prefixes[$prefix] = $baseDir;

        return $this;
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'load']);
    }

    public function load(string $class): void
    {
        foreach ($this->prefixes as $prefix => $baseDir) {
            if (!str_starts_with($class, $prefix)) {
                continue;
            }

            $relative = substr($class, strlen($prefix));
            $file     = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

            if (is_file($file)) {
                require $file;
            }

            return;
        }
    }
}
