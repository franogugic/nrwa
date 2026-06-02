<?php

class MovieController
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

        render('movies/index', [
            'title' => 'Katalog filmova',
            'movies' => $movies,
            'search' => $search,
        ]);
    }

    public function show(int $id): void
    {
        $movie = $this->movies->findById($id);

        if (!$movie) {
            http_response_code(404);
            render('errors/404', ['title' => 'Film nije pronaden']);
            return;
        }

        render('movies/show', [
            'title' => $movie['naslov'],
            'movie' => $movie,
        ]);
    }
}
