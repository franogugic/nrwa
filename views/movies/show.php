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

        .login-prompt {
            background: #1f1f1f;
            padding: 18px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #aaa;
        }
        .login-prompt a { color: #e50914; text-decoration: none; }
        .login-prompt a:hover { text-decoration: underline; }

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
        <a href="index.php?page=my-list" id="nav-mylist" style="display:none;">Moja lista</a>
        <a href="index.php?page=admin" id="nav-admin" style="display:none; color:#f5c518;">Admin</a>
        <div class="nav-right">
            <span id="nav-name" style="color:#aaa; font-size:14px; display:none;"></span>
            <button class="btn-logout" id="btn-logout" onclick="logout()" style="display:none;">Odjava</button>
            <a href="index.php?page=login" class="btn-nav-login" id="btn-login">Prijava</a>
        </div>
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

            <div id="add-section">
                <!-- JS popunjava ovisno o auth stanju -->
            </div>
            <div id="msg"></div>

            <br>
            <a href="index.php" class="back">&larr; Povratak na katalog</a>
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

            document.getElementById('add-section').innerHTML = `
                <div class="add-form">
                    <label style="color:#aaa; font-size:14px;">Dodaj na listu:</label>
                    <select id="status">
                        <option value="want_to_watch">Želim pogledati</option>
                        <option value="watched">Pogledano</option>
                    </select>
                    <button onclick="addToList(<?= (int) $movie['id'] ?>)">Dodaj</button>
                </div>
            `;
        } else {
            document.getElementById('add-section').innerHTML = `
                <div class="login-prompt">
                    <a href="index.php?page=login">Prijavi se</a> kako bi dodao film na svoju listu.
                </div>
            `;
        }

        function addToList(movieId) {
            const status = document.getElementById('status').value;
            const msg    = document.getElementById('msg');

            fetch('/movielist/public/index.php/api/user-movies', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
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

        function logout() {
            localStorage.removeItem('ml_token');
            localStorage.removeItem('ml_name');
            localStorage.removeItem('ml_role');
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>
