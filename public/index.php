<?php

require_once __DIR__ . '/../controllers/MovieController.php';
require_once __DIR__ . '/../controllers/UserMovieController.php';

$page = $_GET['page'] ?? 'catalog';
$id   = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($page === 'my-list') {
    $controller = new UserMovieController();
    $controller->index();
} elseif ($page === 'movie' && $id !== null) {
    $controller = new MovieController();
    $controller->show($id);
} else {
    $controller = new MovieController();
    $controller->index();
}
