<?php

require_once __DIR__ . '/../controllers/MovieController.php';
require_once __DIR__ . '/../controllers/UserMovieController.php';
require_once __DIR__ . '/../controllers/ApiMovieController.php';
require_once __DIR__ . '/../controllers/ApiUserMovieController.php';
require_once __DIR__ . '/../controllers/ApiAuthController.php';
require_once __DIR__ . '/../controllers/ApiAdminMovieController.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Makni base path iz URI-ja
$uri = str_replace(['/movielist/public/index.php', '/movielist/public'], '', $uri);
$uri = $uri === '' ? '/' : $uri;

// API rute
if (str_starts_with($uri, '/api/')) {

    // POST /api/auth/register
    if ($method === 'POST' && $uri === '/api/auth/register') {
        (new ApiAuthController())->register();
        exit;
    }

    // POST /api/auth/login
    if ($method === 'POST' && $uri === '/api/auth/login') {
        (new ApiAuthController())->login();
        exit;
    }

    // POST /api/admin/movies
    if ($method === 'POST' && $uri === '/api/admin/movies') {
        (new ApiAdminMovieController())->store();
        exit;
    }

    // PUT /api/admin/movies/{id}
    if ($method === 'PUT' && preg_match('#^/api/admin/movies/(\d+)$#', $uri, $m)) {
        (new ApiAdminMovieController())->update((int) $m[1]);
        exit;
    }

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

    // GET /api/movies/{id}/comments
    if ($method === 'GET' && preg_match('#^/api/movies/(\d+)/comments$#', $uri, $m)) {
        (new ApiMovieController())->comments((int) $m[1]);
        exit;
    }

    // GET /api/user-movies/by-movie/{id}
    if ($method === 'GET' && preg_match('#^/api/user-movies/by-movie/(\d+)$#', $uri, $m)) {
        (new ApiUserMovieController())->getByMovie((int) $m[1]);
        exit;
    }

    // GET /api/user-movies
    if ($method === 'GET' && $uri === '/api/user-movies') {
        (new ApiUserMovieController())->index();
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
} elseif ($page === 'login') {
    require __DIR__ . '/../views/auth/login.php';
} elseif ($page === 'register') {
    require __DIR__ . '/../views/auth/register.php';
} elseif ($page === 'admin') {
    require __DIR__ . '/../views/admin/movies.php';
} elseif ($page === 'movie' && $id !== null) {
    (new MovieController())->show($id);
} else {
    (new MovieController())->index();
}
