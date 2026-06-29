<?php

require_once __DIR__ . '/../models/MovieModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ApiAdminMovieController
{
    private MovieModel $movieModel;

    public function __construct()
    {
        $this->movieModel = new MovieModel();
    }

    public function store(): void
    {
        $payload = AuthMiddleware::requireAdmin();
        $data    = json_decode(file_get_contents('php://input'), true);

        $title       = trim($data['title'] ?? '');
        $director    = trim($data['director'] ?? '');
        $releaseYear = isset($data['release_year']) ? (int) $data['release_year'] : 0;
        $genre       = trim($data['genre'] ?? '');

        if (!$title || !$director || !$releaseYear || !$genre) {
            $this->json(['error' => 'Naslov, redatelj, godina i žanr su obavezni.'], 400);
            return;
        }

        $id = $this->movieModel->create([
            'title'            => $title,
            'director'         => $director,
            'release_year'     => $releaseYear,
            'genre'            => $genre,
            'description'      => $data['description'] ?? null,
            'added_by_user_id' => $payload['user_id'],
        ]);

        $this->json(['message' => 'Film uspješno dodan u katalog.', 'id' => $id], 201);
    }

    public function update(int $id): void
    {
        AuthMiddleware::requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);

        $title       = trim($data['title'] ?? '');
        $director    = trim($data['director'] ?? '');
        $releaseYear = isset($data['release_year']) ? (int) $data['release_year'] : 0;
        $genre       = trim($data['genre'] ?? '');

        if (!$title || !$director || !$releaseYear || !$genre) {
            $this->json(['error' => 'Naslov, redatelj, godina i žanr su obavezni.'], 400);
            return;
        }

        $this->movieModel->update($id, [
            'title'        => $title,
            'director'     => $director,
            'release_year' => $releaseYear,
            'genre'        => $genre,
            'description'  => $data['description'] ?? null,
        ]);

        $this->json(['message' => 'Film uspješno ažuriran.']);
    }

    private function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
