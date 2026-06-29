<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/Jwt.php';

class ApiAuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $name     = trim($data['name'] ?? '');
        $email    = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (!$name || !$email || !$password) {
            $this->json(['error' => 'Sva polja su obavezna.'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['error' => 'Neispravan format emaila.'], 400);
            return;
        }

        if (strlen($password) < 6) {
            $this->json(['error' => 'Lozinka mora imati najmanje 6 znakova.'], 400);
            return;
        }

        if ($this->userModel->findByEmail($email) !== null) {
            $this->json(['error' => 'Email je već registriran.'], 400);
            return;
        }

        $id    = $this->userModel->create(compact('name', 'email', 'password'));
        $token = Jwt::encode(['user_id' => $id, 'role' => 'user', 'name' => $name]);

        $this->json(['token' => $token, 'name' => $name, 'role' => 'user'], 201);
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $email    = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $this->json(['error' => 'Neispravan email ili lozinka.'], 401);
            return;
        }

        $token = Jwt::encode([
            'user_id' => (int) $user['id'],
            'role'    => $user['role'],
            'name'    => $user['name'],
        ]);

        $this->json(['token' => $token, 'name' => $user['name'], 'role' => $user['role']]);
    }

    private function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
