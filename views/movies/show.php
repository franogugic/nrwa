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

        .movie-info h1 { font-size: 32px; margin-bottom: 10px; }
        .meta { color: #aaa; font-size: 14px; margin-bottom: 10px; }
        .rating { color: #f5c518; font-size: 20px; margin-bottom: 15px; }
        .description { color: #ccc; line-height: 1.7; margin-bottom: 30px; }

        .add-form {
            background: #1f1f1f;
            padding: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        select {
            background: #333;
            color: #fff;
            border: 1px solid #555;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background: #e50914;
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        button:hover { background: #b00710; }

        #msg {
            margin-top: 10px;
            font-size: 14px;
            min-height: 20px;
        }
        .success { color: #27ae60; }
        .error { color: #e50914; }

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
        <div class="movie-info">
            <h1><?= htmlspecialchars($movie['title']) ?></h1>
            <div class="meta">
                <?= (int) $movie['release_year'] ?> &bull;
                <?= htmlspecialchars($movie['genre']) ?> &bull;
                <?= htmlspecialchars($movie['director']) ?>
            </div>
            <div class="rating">
                <?php if ($movie['rating_count'] > 0): ?>
                    ★ <?= htmlspecialchars((string) $movie['avg_rating']) ?> / 5
                    <span style="color:#aaa; font-size:14px;">(<?= (int) $movie['rating_count'] ?> ocjena)</span>
                <?php else: ?>
                    <span style="color:#666;">Još nema ocjena</span>
                <?php endif; ?>
            </div>
            <div class="description"><?= htmlspecialchars($movie['description'] ?? '') ?></div>

            <div class="add-form">
                <label style="color:#aaa; font-size:14px;">Dodaj na listu:</label>
                <select id="status">
                    <option value="want_to_watch">Želim pogledati</option>
                    <option value="watched">Pogledano</option>
                </select>
                <button onclick="addToList(<?= (int) $movie['id'] ?>)">Dodaj</button>
            </div>
            <div id="msg"></div>

            <br>
            <a href="index.php" class="back">&larr; Povratak na katalog</a>
        </div>
    </div>

    <script>
        function addToList(movieId) {
            const status = document.getElementById('status').value;
            const msg = document.getElementById('msg');

            fetch('/movielist/public/index.php/api/user-movies', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ movie_id: movieId, status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    msg.className = 'error';
                    msg.textContent = data.error;
                } else {
                    msg.className = 'success';
                    msg.textContent = 'Film uspješno dodan na listu!';
                }
            })
            .catch(() => {
                msg.className = 'error';
                msg.textContent = 'Greška pri komunikaciji s API-jem.';
            });
        }
    </script>
</body>
</html>
