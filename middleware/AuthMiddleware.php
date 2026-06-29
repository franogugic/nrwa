<?php

require_once __DIR__ . '/../helpers/Jwt.php';

class AuthMiddleware
{
    public static function require(): array
    {
        $payload = self::getPayload();

        if ($payload === null) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Niste autorizirani. Potrebna je prijava.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        return $payload;
    }

    public static function requireAdmin(): array
    {
        $payload = self::require();

        if ($payload['role'] !== 'admin') {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Pristup zabranjen. Potrebne su admin ovlasti.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        return $payload;
    }

    public static function optional(): ?array
    {
        return self::getPayload();
    }

    private static function getPayload(): ?array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!str_starts_with($header, 'Bearer ')) return null;
        $token = substr($header, 7);
        return Jwt::decode($token);
    }
}
