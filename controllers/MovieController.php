<?php

require_once __DIR__ . '/../models/MovieModel.php';

class MovieController
{
    private MovieModel $movieModel;

    public function __construct()
    {
        $this->movieModel = new MovieModel();
    }

    public function index(): void
    {
        $movies = $this->movieModel->findAll();
        require __DIR__ . '/../views/movies/index.php';
    }

    public function show(int $id): void
    {
        $movie = $this->movieModel->findById($id);
        require __DIR__ . '/../views/movies/show.php';
    }
}
