<?php

require_once __DIR__ . '/../config/database.php';

class UserMovieModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM user_movies WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findByUserAndMovie(int $userId, int $movieId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user_movies WHERE user_id = ? AND movie_id = ?");
        $stmt->execute([$userId, $movieId]);
        $record = $stmt->fetch();

        return $record ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO user_movies (status, rating, comment, watched_at, user_id, movie_id)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['status'],
            $data['rating'] ?? null,
            $data['comment'] ?? null,
            $data['watched_at'] ?? null,
            $data['user_id'],
            $data['movie_id'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE user_movies SET status = ?, rating = ?, comment = ?, watched_at = ? WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['status'],
            $data['rating'] ?? null,
            $data['comment'] ?? null,
            $data['watched_at'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user_movies WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
