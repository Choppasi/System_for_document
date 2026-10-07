<?php

class Router
{
    private array $routes = [];

    public function add(string|array $methods, string $path, callable $handler): void
    {
        foreach ((array)$methods as $method) {
            $this->routes[$method][$path] = $handler;
        }
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path   = $this->normalize($_SERVER['REQUEST_URI'] ?? '/');

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        $handler();
    }

    private function normalize(string $uri): string
    {
        $path = '/' . trim(parse_url($uri, PHP_URL_PATH) ?: '/', '/');

        return $path === '/index.php' ? '/' : $path;
    }
}
