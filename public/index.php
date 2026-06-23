<?php

require_once __DIR__ . '/../controllers/MovieController.php';
require_once __DIR__ . '/../controllers/UserMovieController.php';
require_once __DIR__ . '/../controllers/ApiMovieController.php';
require_once __DIR__ . '/../controllers/ApiUserMovieController.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Makni base path i index.php iz URI-ja
$uri = str_replace(['/movielist/public/index.php', '/movielist/public'], '', $uri);
$uri = $uri === '' ? '/' : $uri;

// API rute
if (str_starts_with($uri, '/api/')) {

    // GET /api/movies
    if ($method === 'GET' && $uri === '/api/movies') {
        (new ApiMovieController())->index();
        exit;
    }

    // GET /api/movies/{id}
    if ($method === 'GET' && preg_match('#^/api/movies/(\d+)$#', $uri, $m)) {
        (new ApiMovieController())->show((int) $m[1]);
        exit;
    }

    // POST /api/user-movies
    if ($method === 'POST' && $uri === '/api/user-movies') {
        (new ApiUserMovieController())->store();
        exit;
    }

    // PUT /api/user-movies/{id}
    if ($method === 'PUT' && preg_match('#^/api/user-movies/(\d+)$#', $uri, $m)) {
        (new ApiUserMovieController())->update((int) $m[1]);
        exit;
    }

    // DELETE /api/user-movies/{id}
    if ($method === 'DELETE' && preg_match('#^/api/user-movies/(\d+)$#', $uri, $m)) {
        (new ApiUserMovieController())->destroy((int) $m[1]);
        exit;
    }

    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Ruta nije pronađena.']);
    exit;
}

// Web rute
$page = $_GET['page'] ?? 'catalog';
$id   = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($page === 'my-list') {
    (new UserMovieController())->index();
} elseif ($page === 'movie' && $id !== null) {
    (new MovieController())->show($id);
} else {
    (new MovieController())->index();
}
