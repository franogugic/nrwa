<?php

class ApiUserMovieController
{
    private const DEMO_USER_ID = 1;

    private UserMovieModel $userMovies;
    private MovieModel $movies;

    public function __construct(UserMovieModel $userMovies, MovieModel $movies)
    {
        $this->userMovies = $userMovies;
        $this->movies = $movies;
    }

    public function store(): void
    {
        $data = jsonInput();
        $movieId = (int) ($data['film_id'] ?? $data['movie_id'] ?? 0);
        $status = $data['status'] ?? 'want_to_watch';

        if ($movieId <= 0) {
            jsonResponse(['error' => 'Polje film_id je obavezno.'], 400);
            return;
        }

        if (!$this->movies->findById($movieId)) {
            jsonResponse(['error' => 'Film nije pronaden.'], 404);
            return;
        }

        if (!in_array($status, ['watched', 'want_to_watch'], true)) {
            jsonResponse(['error' => "Status mora biti 'watched' ili 'want_to_watch'."], 400);
            return;
        }

        if ($this->userMovies->findByUserAndMovie(self::DEMO_USER_ID, $movieId)) {
            jsonResponse(['error' => 'Film je vec dodan na osobnu listu.'], 409);
            return;
        }

        $rating = $this->normalizeRating($data['ocjena'] ?? $data['rating'] ?? null);

        if ($rating === false) {
            jsonResponse(['error' => 'Ocjena mora biti broj od 1 do 5 ili prazna.'], 400);
            return;
        }

        $id = $this->userMovies->create([
            'status' => $status,
            'ocjena' => $rating,
            'komentar' => $data['komentar'] ?? $data['comment'] ?? null,
            'datum_gledanja' => $data['datum_gledanja'] ?? $data['watched_at'] ?? null,
            'korisnik_id' => self::DEMO_USER_ID,
            'film_id' => $movieId,
        ]);

        jsonResponse([
            'message' => 'Film je dodan na osobnu listu.',
            'data' => $this->userMovies->findById($id),
        ], 201);
    }

    public function update(int $id): void
    {
        $record = $this->userMovies->findById($id);

        if (!$record) {
            jsonResponse(['error' => 'Zapis nije pronaden.'], 404);
            return;
        }

        if ((int) $record['korisnik_id'] !== self::DEMO_USER_ID) {
            jsonResponse(['error' => 'Nije dozvoljeno mijenjati ovaj zapis.'], 403);
            return;
        }

        $data = jsonInput();
        $status = $data['status'] ?? $record['status'];

        if (!in_array($status, ['watched', 'want_to_watch'], true)) {
            jsonResponse(['error' => "Status mora biti 'watched' ili 'want_to_watch'."], 400);
            return;
        }

        $rating = $this->normalizeRating($data['ocjena'] ?? $data['rating'] ?? $record['ocjena']);

        if ($rating === false) {
            jsonResponse(['error' => 'Ocjena mora biti broj od 1 do 5 ili prazna.'], 400);
            return;
        }

        $this->userMovies->update($id, [
            'status' => $status,
            'ocjena' => $rating,
            'komentar' => $data['komentar'] ?? $data['comment'] ?? $record['komentar'],
            'datum_gledanja' => $data['datum_gledanja'] ?? $data['watched_at'] ?? $record['datum_gledanja'],
        ]);

        jsonResponse([
            'message' => 'Zapis je azuriran.',
            'data' => $this->userMovies->findById($id),
        ]);
    }

    public function destroy(int $id): void
    {
        $record = $this->userMovies->findById($id);

        if (!$record) {
            jsonResponse(['error' => 'Zapis nije pronaden.'], 404);
            return;
        }

        if ((int) $record['korisnik_id'] !== self::DEMO_USER_ID) {
            jsonResponse(['error' => 'Nije dozvoljeno brisati ovaj zapis.'], 403);
            return;
        }

        $this->userMovies->delete($id);

        jsonResponse(['message' => 'Film je uklonjen s osobne liste.']);
    }

    private function normalizeRating($rating)
    {
        if ($rating === null || $rating === '') {
            return null;
        }

        if (!is_numeric($rating)) {
            return false;
        }

        $rating = (int) $rating;

        if ($rating < 1 || $rating > 5) {
            return false;
        }

        return $rating;
    }
}
