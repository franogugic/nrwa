<?php

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/config/Connection.php';
require __DIR__ . '/../src/Router.php';
require __DIR__ . '/../src/models/MovieModel.php';
require __DIR__ . '/../src/models/UserMovieModel.php';
require __DIR__ . '/../src/controllers/MovieController.php';
require __DIR__ . '/../src/controllers/UserMovieController.php';
require __DIR__ . '/../src/controllers/ApiMovieController.php';
require __DIR__ . '/../src/controllers/ApiUserMovieController.php';

$db = Connection::get();

$movieModel = new MovieModel($db);
$userMovieModel = new UserMovieModel($db);

$movieController = new MovieController($movieModel);
$userMovieController = new UserMovieController($userMovieModel);
$apiMovieController = new ApiMovieController($movieModel);
$apiUserMovieController = new ApiUserMovieController($userMovieModel, $movieModel);

$router = new Router();
$router->get('/', [$movieController, 'index']);
$router->get('/movies', [$movieController, 'index']);
$router->get('/movies/{id}', [$movieController, 'show']);
$router->get('/my-list', [$userMovieController, 'index']);

$router->get('/api/movies', [$apiMovieController, 'index']);
$router->get('/api/movies/{id}', [$apiMovieController, 'show']);
$router->post('/api/user-movies', [$apiUserMovieController, 'store']);
$router->put('/api/user-movies/{id}', [$apiUserMovieController, 'update']);
$router->delete('/api/user-movies/{id}', [$apiUserMovieController, 'destroy']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
