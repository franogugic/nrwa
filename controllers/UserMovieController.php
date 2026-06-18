<?php

require_once __DIR__ . '/../models/UserMovieModel.php';

class UserMovieController
{
    private UserMovieModel $userMovieModel;

    public function __construct()
    {
        $this->userMovieModel = new UserMovieModel();
    }

    public function index(): void
    {
        // user_id = 1 privremeno dok nema autentikacije
        $entries = $this->userMovieModel->findByUser(1);
        require __DIR__ . '/../views/user_movies/index.php';
    }
}
