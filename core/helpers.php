<?php

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}



function redirect(string $url): void
{
    header('Location: '.$url);
    exit;
}

function old(string $field, mixed $fallback = ''): string
{
    return e($_POST[$field] ?? $fallback);
}

function selected(mixed $a, mixed $b): string
{
    return (string)$a === (string)$b ? 'selected' : '';
}

function fmt_date(?string $dt): string
{
    return $dt ? date('d.m.Y H:i', strtotime($dt)) : '-';
}


function qs(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);
    $params = array_filter($params, static fn($v) => $v !== '' && $v !== null);
    return $params === [] ? 'index.php' : 'index.php?'.http_build_query($params);
} 

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Проверяет CSRF-токен из POST-запроса
function csrf_verify(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}