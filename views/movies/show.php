<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($movie['title']) ?> - MovieList</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #141414; color: #fff; min-height: 100vh; }

        nav {
            background: #000;
            padding: 15px 40px;
            display: flex;
            align-items: center;
            gap: 30px;
            border-bottom: 1px solid #333;
        }
        nav .logo { font-size: 22px; font-weight: bold; color: #e50914; letter-spacing: 2px; }
        nav a { color: #ccc; text-decoration: none; font-size: 14px; }
        nav a:hover { color: #fff; }

        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }

        .movie-header {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }

        .movie-info h1 { font-size: 28px; margin-bottom: 10px; }
        .meta { color: #aaa; font-size: 14px; margin-bottom: 8px; }
        .rating { color: #f5c518; font-size: 18px; margin-bottom: 15px; }
        .description { color: #ccc; line-height: 1.6; margin-bottom: 20px; }

        .back { color: #aaa; text-decoration: none; font-size: 14px; }
        .back:hover { color: #fff; }
    </style>
</head>
<body>
    <nav>
        <span class="logo">MOVIELIST</span>
        <a href="index.php">Katalog</a>
        <a href="index.php?page=my-list">Moja lista</a>
    </nav>

    <div class="container">
        <div class="movie-header">
            <div class="movie-info">
                <h1><?= htmlspecialchars($movie['title']) ?></h1>
                <div class="meta"><?= (int) $movie['release_year'] ?> &bull; <?= htmlspecialchars($movie['genre']) ?> &bull; <?= htmlspecialchars($movie['director']) ?></div>
                <div class="rating">
                    <?php if ($movie['rating_count'] > 0): ?>
                        ★ <?= htmlspecialchars((string) $movie['avg_rating']) ?> / 5
                        <span style="color:#aaa; font-size:14px;">(<?= (int) $movie['rating_count'] ?> ocjena)</span>
                    <?php else: ?>
                        <span style="color:#666;">Još nema ocjena</span>
                    <?php endif; ?>
                </div>
                <div class="description"><?= htmlspecialchars($movie['description'] ?? '') ?></div>
                <a href="index.php" class="back">&larr; Povratak na katalog</a>
            </div>
        </div>
    </div>
</body>
</html>
