<?php

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);

    ob_start();
    require __DIR__ . '/views/' . $view . '.php';
    $content = ob_get_clean();

    require __DIR__ . '/views/layout.php';
}
