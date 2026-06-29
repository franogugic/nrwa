<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>MovieList - Katalog</title>
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
        nav .nav-right { margin-left: auto; display: flex; align-items: center; gap: 16px; }

        .btn-nav-login {
            background: #e50914;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-nav-login:hover { background: #b00710; }
        .btn-logout { background: none; border: 1px solid #555; color: #ccc; padding: 5px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .btn-logout:hover { border-color: #e50914; color: #fff; }

        .container { max-width: 1100px; margin: 0 auto; padding: 40px 20px; }
        h1 { font-size: 28px; margin-bottom: 25px; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .card {
            background: #1f1f1f;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
            cursor: pointer;
        }
        .card:hover { transform: scale(1.03); }

        .card-body { padding: 12px; }
        .card-title { font-size: 14px; font-weight: bold; margin-bottom: 4px; }
        .card-meta { font-size: 12px; color: #aaa; margin-bottom: 6px; }

        .rating { color: #f5c518; font-size: 13px; }
        .no-rating { color: #666; font-size: 12px; }

        .card a {
            display: block;
            text-align: center;
            margin-top: 10px;
            padding: 6px;
            background: #e50914;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
        }
        .card a:hover { background: #b00710; }
    </style>
</head>
<body>
    <nav>
        <span class="logo">MOVIELIST</span>
        <a href="index.php">Katalog</a>
        <a href="index.php?page=my-list" id="nav-mylist" style="display:none;">Moja lista</a>
        <a href="index.php?page=admin" id="nav-admin" style="display:none; color:#f5c518;">Admin</a>
        <div class="nav-right">
            <span id="nav-name" style="color:#aaa; font-size:14px; display:none;"></span>
            <button class="btn-logout" id="btn-logout" onclick="logout()" style="display:none;">Odjava</button>
            <a href="index.php?page=login" class="btn-nav-login" id="btn-login">Prijava</a>
        </div>
    </nav>

    <div class="container">
        <h1>Katalog filmova</h1>
        <div class="grid">
            <?php foreach ($movies as $movie): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="card-title"><?= htmlspecialchars($movie['title']) ?></div>
                        <div class="card-meta"><?= (int) $movie['release_year'] ?> &bull; <?= htmlspecialchars($movie['genre']) ?></div>
                        <div>
                            <?php if ($movie['rating_count'] > 0): ?>
                                <span class="rating">★ <?= htmlspecialchars((string) $movie['avg_rating']) ?> / 5</span>
                            <?php else: ?>
                                <span class="no-rating">Bez ocjene</span>
                            <?php endif; ?>
                        </div>
                        <a href="index.php?page=movie&id=<?= (int) $movie['id'] ?>">Detalji</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('ml_token');
        const name  = localStorage.getItem('ml_name');
        const role  = localStorage.getItem('ml_role');

        if (token) {
            document.getElementById('nav-name').textContent = name;
            document.getElementById('nav-name').style.display = 'inline';
            document.getElementById('btn-logout').style.display = 'inline-block';
            document.getElementById('btn-login').style.display = 'none';
            document.getElementById('nav-mylist').style.display = 'inline';
            if (role === 'admin') {
                document.getElementById('nav-admin').style.display = 'inline';
            }
        }

        function logout() {
            localStorage.removeItem('ml_token');
            localStorage.removeItem('ml_name');
            localStorage.removeItem('ml_role');
            window.location.reload();
        }
    </script>
</body>
</html>
