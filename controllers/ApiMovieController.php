<?php

require_once __DIR__ . '/../models/MovieModel.php';

class ApiMovieController
{
    private MovieModel $movieModel;

    public function __construct()
    {
        $this->movieModel = new MovieModel();
    }

    public function index(): void
    {
        $search = $_GET['search'] ?? '';

        if ($search !== '') {
            $movies = $this->movieModel->search($search);
        } else {
            $movies = $this->movieModel->findAll();
        }

        $this->json($movies);
    }

    public function show(int $id): void
    {
        $movie = $this->movieModel->findById($id);

        if ($movie === null) {
            $this->json(['error' => 'Film nije pronađen.'], 404);
            return;
        }

        $this->json($movie);
    }

    private function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
