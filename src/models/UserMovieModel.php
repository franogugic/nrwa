<?php

class UserMovieModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByUser(int $userId): array
    {
        $sql = "
            SELECT kf.*, f.naslov, f.redatelj, f.godina, f.zanr
            FROM korisnik_filmovi kf
            INNER JOIN filmovi f ON f.id = kf.film_id
            WHERE kf.korisnik_id = :korisnik_id
            ORDER BY kf.status, f.naslov
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute(['korisnik_id' => $userId]);

        return $statement->fetchAll();
    }

    public function findByUserAndMovie(int $userId, int $movieId): ?array
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM korisnik_filmovi
            WHERE korisnik_id = :korisnik_id AND film_id = :film_id
        ");
        $statement->execute([
            'korisnik_id' => $userId,
            'film_id' => $movieId,
        ]);
        $record = $statement->fetch();

        return $record ?: null;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare("
            SELECT kf.*, f.naslov, f.redatelj, f.godina, f.zanr
            FROM korisnik_filmovi kf
            INNER JOIN filmovi f ON f.id = kf.film_id
            WHERE kf.id = :id
        ");
        $statement->execute(['id' => $id]);
        $record = $statement->fetch();

        return $record ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO korisnik_filmovi (status, ocjena, komentar, datum_gledanja, korisnik_id, film_id)
            VALUES (:status, :ocjena, :komentar, :datum_gledanja, :korisnik_id, :film_id)
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'status' => $data['status'],
            'ocjena' => $data['ocjena'] ?? null,
            'komentar' => $data['komentar'] ?? null,
            'datum_gledanja' => $data['datum_gledanja'] ?? null,
            'korisnik_id' => $data['korisnik_id'],
            'film_id' => $data['film_id'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE korisnik_filmovi
            SET status = :status,
                ocjena = :ocjena,
                komentar = :komentar,
                datum_gledanja = :datum_gledanja
            WHERE id = :id
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            'id' => $id,
            'status' => $data['status'],
            'ocjena' => $data['ocjena'] ?? null,
            'komentar' => $data['komentar'] ?? null,
            'datum_gledanja' => $data['datum_gledanja'] ?? null,
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM korisnik_filmovi WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }
}
