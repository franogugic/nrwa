<?php

class ApiMovieController
{
    private MovieModel $movies;

    public function __construct(MovieModel $movies)
    {
        $this->movies = $movies;
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $movies = $search !== '' ? $this->movies->search($search) : $this->movies->findAll();

        jsonResponse([
            'data' => $movies,
            'meta' => [
                'count' => count($movies),
                'search' => $search !== '' ? $search : null,
            ],
        ]);
    }

    public function show(int $id): void
    {
        $movie = $this->movies->findById($id);

        if (!$movie) {
            jsonResponse(['error' => 'Film nije pronaden.'], 404);
            return;
        }

        jsonResponse(['data' => $movie]);
    }
}
