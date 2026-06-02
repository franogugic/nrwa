<?php

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/config/Connection.php';
require __DIR__ . '/../src/Router.php';
require __DIR__ . '/../src/models/MovieModel.php';
require __DIR__ . '/../src/models/UserMovieModel.php';
require __DIR__ . '/../src/controllers/MovieController.php';
require __DIR__ . '/../src/controllers/UserMovieController.php';

$db = Connection::get();

$movieController = new MovieController(new MovieModel($db));
$userMovieController = new UserMovieController(new UserMovieModel($db));

$router = new Router();
$router->get('/', [$movieController, 'index']);
$router->get('/movies', [$movieController, 'index']);
$router->get('/movies/{id}', [$movieController, 'show']);
$router->get('/my-list', [$userMovieController, 'index']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
