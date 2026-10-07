<?php

class Request
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->value($_GET, $key, $default);
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->value($_POST, $key, $default);
    }

    public function getString(string $key, string $default = ''): string
    {
        return $this->string($this->get($key), $default);
    }

    public function postString(string $key, string $default = ''): string
    {
        return $this->string($this->post($key), $default);
    }

    public function getInt(string $key, int $default = 0): int
    {
        return $this->int($this->get($key), $default);
    }

    public function postInt(string $key, int $default = 0): int
    {
        return $this->int($this->post($key), $default);
    }

    public function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public function verifyCsrf(): bool
    {
        $token = $this->post('csrf_token');

        return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    public function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars($this->csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    private function value(array $source, string $key, mixed $default): mixed
    {
        if (!array_key_exists($key, $source)) {
            return $default;
        }

        $value = $source[$key];

        return is_scalar($value) ? $value : $default;
    }

    private function string(mixed $value, string $default): string
    {
        return is_scalar($value) ? trim((string)$value) : $default;
    }

    private function int(mixed $value, int $default): int
    {
        return is_numeric($value) ? (int)$value : $default;
    }
}
