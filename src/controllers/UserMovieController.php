<?php

class UserMovieController
{
    private const DEMO_USER_ID = 1;

    private UserMovieModel $userMovies;

    public function __construct(UserMovieModel $userMovies)
    {
        $this->userMovies = $userMovies;
    }

    public function index(): void
    {
        $records = $this->userMovies->findByUser(self::DEMO_USER_ID);

        render('user_movies/index', [
            'title' => 'Moja lista filmova',
            'records' => $records,
        ]);
    }
}
