<?php

require_once __DIR__ . '/../models/UserMovieModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ApiUserMovieController
{
    private UserMovieModel $userMovieModel;

    public function __construct()
    {
        $this->userMovieModel = new UserMovieModel();
    }

    public function index(): void
    {
        $payload = AuthMiddleware::require();
        $entries = $this->userMovieModel->findByUserWithMovies($payload['user_id']);
        $this->json($entries);
    }

    public function getByMovie(int $movieId): void
    {
        $payload = AuthMiddleware::require();
        $entry   = $this->userMovieModel->findByUserAndMovie($payload['user_id'], $movieId);
        $this->json($entry ?? (object)[]);
    }

    public function store(): void
    {
        $payload = AuthMiddleware::require();
        $data    = json_decode(file_get_contents('php://input'), true);

        $movieId = isset($data['movie_id']) ? (int) $data['movie_id'] : 0;
        $status  = $data['status'] ?? 'want_to_watch';

        if ($movieId === 0) {
            $this->json(['error' => 'movie_id je obavezan.'], 400);
            return;
        }

        if (!in_array($status, ['watched', 'want_to_watch'])) {
            $this->json(['error' => 'Status mora biti watched ili want_to_watch.'], 400);
            return;
        }

        $existing = $this->userMovieModel->findByUserAndMovie($payload['user_id'], $movieId);
        if ($existing !== null) {
            $this->json(['error' => 'Film je već na tvojoj listi.'], 400);
            return;
        }

        $id = $this->userMovieModel->create([
            'status'   => $status,
            'user_id'  => $payload['user_id'],
            'movie_id' => $movieId,
        ]);

        $this->json(['message' => 'Film dodan na listu.', 'id' => $id], 201);
    }

    public function update(int $id): void
    {
        $payload = AuthMiddleware::require();
        $data    = json_decode(file_get_contents('php://input'), true);

        $record = $this->userMovieModel->findById($id);
        if ($record === null || (int) $record['user_id'] !== $payload['user_id']) {
            $this->json(['error' => 'Zapis nije pronađen ili nemate ovlasti.'], 403);
            return;
        }

        $status    = $data['status'] ?? 'watched';
        $rating    = isset($data['rating']) && $data['rating'] !== '' ? (int) $data['rating'] : null;
        $comment   = $data['comment'] ?? null;
        $watchedAt = isset($data['watched_at']) && $data['watched_at'] !== '' ? $data['watched_at'] : null;

        $this->userMovieModel->update($id, [
            'status'     => $status,
            'rating'     => $rating,
            'comment'    => $comment,
            'watched_at' => $watchedAt,
        ]);

        $this->json(['message' => 'Zapis ažuriran.']);
    }

    public function destroy(int $id): void
    {
        $payload = AuthMiddleware::require();

        $record = $this->userMovieModel->findById($id);
        if ($record === null || (int) $record['user_id'] !== $payload['user_id']) {
            $this->json(['error' => 'Zapis nije pronađen ili nemate ovlasti.'], 403);
            return;
        }

        $this->userMovieModel->delete($id);
        $this->json(['message' => 'Film uklonjen s liste.']);
    }

    private function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
