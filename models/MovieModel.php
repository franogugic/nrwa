<?php

require_once __DIR__ . '/../config/database.php';

class MovieModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT m.*, ROUND(AVG(um.rating), 1) AS avg_rating, COUNT(um.rating) AS rating_count
                FROM movies m
                LEFT JOIN user_movies um ON um.movie_id = m.id
                GROUP BY m.id
                ORDER BY m.title ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT m.*, ROUND(AVG(um.rating), 1) AS avg_rating, COUNT(um.rating) AS rating_count
                FROM movies m
                LEFT JOIN user_movies um ON um.movie_id = m.id
                WHERE m.id = ?
                GROUP BY m.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $movie = $stmt->fetch();

        return $movie ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO movies (title, director, release_year, genre, description, added_by_user_id)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['title'],
            $data['director'],
            $data['release_year'],
            $data['genre'],
            $data['description'] ?? null,
            $data['added_by_user_id'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE movies SET title = ?, director = ?, release_year = ?, genre = ?, description = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['director'],
            $data['release_year'],
            $data['genre'],
            $data['description'] ?? null,
            $id,
        ]);
    }

    public function search(string $term): array
    {
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY title ASC");
        $stmt->execute(['%' . $term . '%']);
        return $stmt->fetchAll();
    }
}
