<?php

Class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ .'/../views/'.$template.'.php';
        require __DIR__ . '/../views/layouts/footer.php';

    }
}