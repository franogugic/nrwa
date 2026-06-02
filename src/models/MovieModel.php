<?php

class MovieModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $sql = "
            SELECT f.*, rs.prosjecna_ocjena, rs.broj_ocjena
            FROM filmovi f
            LEFT JOIN (
                SELECT film_id, ROUND(AVG(ocjena), 1) AS prosjecna_ocjena, COUNT(ocjena) AS broj_ocjena
                FROM korisnik_filmovi
                WHERE ocjena IS NOT NULL
                GROUP BY film_id
            ) rs ON rs.film_id = f.id
            ORDER BY f.naslov
        ";

        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT f.*, rs.prosjecna_ocjena, rs.broj_ocjena
            FROM filmovi f
            LEFT JOIN (
                SELECT film_id, ROUND(AVG(ocjena), 1) AS prosjecna_ocjena, COUNT(ocjena) AS broj_ocjena
                FROM korisnik_filmovi
                WHERE ocjena IS NOT NULL
                GROUP BY film_id
            ) rs ON rs.film_id = f.id
            WHERE f.id = :id
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
        $movie = $statement->fetch();

        return $movie ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO filmovi (naslov, redatelj, godina, zanr, opis, dodao_korisnik_id)
            VALUES (:naslov, :redatelj, :godina, :zanr, :opis, :dodao_korisnik_id)
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'naslov' => $data['naslov'],
            'redatelj' => $data['redatelj'],
            'godina' => $data['godina'],
            'zanr' => $data['zanr'],
            'opis' => $data['opis'] ?? null,
            'dodao_korisnik_id' => $data['dodao_korisnik_id'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function search(string $query): array
    {
        $sql = "
            SELECT f.*, rs.prosjecna_ocjena, rs.broj_ocjena
            FROM filmovi f
            LEFT JOIN (
                SELECT film_id, ROUND(AVG(ocjena), 1) AS prosjecna_ocjena, COUNT(ocjena) AS broj_ocjena
                FROM korisnik_filmovi
                WHERE ocjena IS NOT NULL
                GROUP BY film_id
            ) rs ON rs.film_id = f.id
            WHERE f.naslov LIKE :query OR f.redatelj LIKE :query OR f.zanr LIKE :query
            ORDER BY f.naslov
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute(['query' => '%' . $query . '%']);

        return $statement->fetchAll();
    }
}
