<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($movie['title']) ?> - MovieList</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #141414; color: #fff; min-height: 100vh; }

        nav { background: #000; padding: 15px 40px; display: flex; align-items: center; gap: 30px; border-bottom: 1px solid #333; }
        nav .logo { font-size: 22px; font-weight: bold; color: #e50914; letter-spacing: 2px; }
        nav a { color: #ccc; text-decoration: none; font-size: 14px; }
        nav a:hover { color: #fff; }
        nav .nav-right { margin-left: auto; display: flex; align-items: center; gap: 16px; }
        .btn-nav-login { background: #e50914; color: #fff; border: none; padding: 6px 16px; border-radius: 4px; font-size: 13px; cursor: pointer; text-decoration: none; }
        .btn-nav-login:hover { background: #b00710; }
        .btn-logout { background: none; border: 1px solid #555; color: #ccc; padding: 5px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .btn-logout:hover { border-color: #e50914; color: #fff; }

        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .movie-info h1 { font-size: 32px; margin-bottom: 10px; }
        .meta { color: #aaa; font-size: 14px; margin-bottom: 10px; }
        .avg-rating { color: #f5c518; font-size: 20px; margin-bottom: 15px; }
        .description { color: #ccc; line-height: 1.7; margin-bottom: 30px; }

        .action-box { background: #1f1f1f; padding: 20px; border-radius: 8px; margin-bottom: 15px; }
        .action-box h3 { font-size: 15px; margin-bottom: 15px; color: #aaa; }

        .stars { display: flex; gap: 6px; font-size: 32px; cursor: pointer; margin-bottom: 12px; }
        .star { color: #555; transition: color 0.1s; }
        .star.active { color: #f5c518; }
        .star:hover { color: #f5c518; }

        textarea { resize: vertical; width: 100%; height: 70px; margin-bottom: 12px; background: #333; color: #fff; border: 1px solid #555; padding: 8px 12px; border-radius: 4px; font-size: 14px; }

        .btn-green { background: #27ae60; color: #fff; border: none; padding: 9px 20px; border-radius: 4px; font-size: 14px; cursor: pointer; }
        .btn-green:hover { background: #1e8449; }

        #msg { margin-top: 10px; font-size: 14px; min-height: 20px; }
        .success { color: #27ae60; }
        .error { color: #e50914; }

        .login-prompt { background: #1f1f1f; padding: 18px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; color: #aaa; }
        .login-prompt a { color: #e50914; text-decoration: none; }

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
            <div class="avg-rating" id="avg-rating">
                <?php if ($movie['rating_count'] > 0): ?>
                    ★ <?= htmlspecialchars((string) $movie['avg_rating']) ?> / 5
                    <span style="color:#aaa; font-size:14px;">(<?= (int) $movie['rating_count'] ?> ocjena)</span>
                <?php else: ?>
                    <span style="color:#666;">Još nema ocjena</span>
                <?php endif; ?>
            </div>
            <div class="description"><?= htmlspecialchars($movie['description'] ?? '') ?></div>

            <div id="action-section"></div>
            <div id="msg"></div>

            <h3 style="margin-top:30px; margin-bottom:15px; color:#aaa;">Komentari</h3>
            <div id="comments-section"></div>

            <br>
            <a href="index.php" class="back">&larr; Povratak na katalog</a>
        </div>
    </div>

    <script>
        const token   = localStorage.getItem('ml_token');
        const name    = localStorage.getItem('ml_name');
        const role    = localStorage.getItem('ml_role');
        const movieId = <?= (int) $movie['id'] ?>;
        let selectedRating = 0;
        let existingEntryId = null;
        let existingStatus  = 'watched';

        if (token) {
            document.getElementById('nav-name').textContent = name;
            document.getElementById('nav-name').style.display = 'inline';
            document.getElementById('btn-logout').style.display = 'inline-block';
            document.getElementById('btn-login').style.display = 'none';
            document.getElementById('nav-mylist').style.display = 'inline';
            if (role === 'admin') document.getElementById('nav-admin').style.display = 'inline';

            loadUserEntry();
        } else {
            document.getElementById('action-section').innerHTML = `
                <div class="login-prompt">
                    <a href="index.php?page=login">Prijavi se</a> kako bi dodao film na listu i ostavio ocjenu.
                </div>`;
        }

        loadComments();

        function loadUserEntry() {
            fetch('/movielist/public/api/user-movies/by-movie/' + movieId, {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(r => r.json())
            .then(entry => {
                if (entry && entry.id) {
                    existingEntryId = entry.id;
                    existingStatus  = entry.status;
                    selectedRating  = entry.rating ?? 0;
                    renderActions(entry);
                } else {
                    renderActions(null);
                }
            })
            .catch(() => renderActions(null));
        }

        function renderActions(entry) {
            const statusVal  = entry ? entry.status : 'want_to_watch';
            const commentVal = entry ? (entry.comment ?? '') : '';

            document.getElementById('action-section').innerHTML = `
                <div class="action-box">
                    <h3>${entry ? 'Tvoj status' : 'Dodaj na listu'}</h3>
                    <div style="display:flex; gap:12px; align-items:center;">
                        <select id="status" style="background:#333; color:#fff; border:1px solid #555; padding:8px 12px; border-radius:4px; font-size:14px;">
                            <option value="want_to_watch" ${statusVal === 'want_to_watch' ? 'selected' : ''}>Želim pogledati</option>
                            <option value="watched" ${statusVal === 'watched' ? 'selected' : ''}>Pogledano</option>
                        </select>
                        <button class="btn-green" onclick="${entry ? 'updateStatus()' : 'addToList()'}">${entry ? 'Ažuriraj' : 'Dodaj'}</button>
                    </div>
                </div>
                <div class="action-box">
                    <h3>Ocijeni film</h3>
                    <div class="stars" id="stars">
                        ${[1,2,3,4,5].map(i => `<span class="star ${i <= selectedRating ? 'active' : ''}" onclick="setRating(${i})">★</span>`).join('')}
                    </div>
                    <textarea id="comment" placeholder="Ostavi komentar (opcionalno)...">${commentVal}</textarea>
                    <button class="btn-green" onclick="submitRating()">Spremi ocjenu</button>
                </div>`;
        }

        function addToList() {
            const status = document.getElementById('status').value;
            fetch('/movielist/public/api/user-movies', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ movie_id: movieId, status })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) showMsg(data.error, 'error');
                else { showMsg('Film dodan na listu!', 'success'); loadUserEntry(); }
            });
        }

        function updateStatus() {
            const status = document.getElementById('status').value;
            fetch('/movielist/public/api/user-movies/' + existingEntryId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ status, rating: selectedRating || null, comment: document.getElementById('comment').value })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) showMsg(data.error, 'error');
                else showMsg('Status ažuriran!', 'success');
            });
        }

        function setRating(n) {
            selectedRating = n;
            document.querySelectorAll('.star').forEach((s, i) => s.classList.toggle('active', i < n));
        }

        function submitRating() {
            if (selectedRating === 0) { showMsg('Odaberi ocjenu (1-5).', 'error'); return; }
            const comment = document.getElementById('comment').value;

            if (existingEntryId) {
                fetch('/movielist/public/api/user-movies/' + existingEntryId, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ status: existingStatus, rating: selectedRating, comment })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.error) showMsg(data.error, 'error');
                    else { showMsg('Ocjena spremljena!', 'success'); loadComments(); }
                });
            } else {
                fetch('/movielist/public/api/user-movies', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ movie_id: movieId, status: 'watched', rating: selectedRating, comment })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.error) showMsg(data.error, 'error');
                    else { showMsg('Ocjena spremljena!', 'success'); loadUserEntry(); loadComments(); }
                });
            }
        }

        function loadComments() {
            fetch('/movielist/public/api/movies/' + movieId + '/comments')
            .then(r => r.json())
            .then(comments => {
                const section = document.getElementById('comments-section');
                if (!comments.length) { section.innerHTML = '<p style="color:#666;">Još nema komentara.</p>'; return; }
                section.innerHTML = comments.map(c => `
                    <div style="background:#1f1f1f; padding:15px; border-radius:8px; margin-bottom:10px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                            <strong>${escapeHtml(c.name)}</strong>
                            <span style="color:#f5c518;">${c.rating ? '★'.repeat(c.rating) : ''}</span>
                        </div>
                        <p style="color:#ccc; font-size:14px;">${escapeHtml(c.comment)}</p>
                        ${c.watched_at ? `<small style="color:#666;">Pogledano: ${escapeHtml(c.watched_at)}</small>` : ''}
                    </div>`).join('');
            });
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function showMsg(text, type) {
            const msg = document.getElementById('msg');
            msg.className = type;
            msg.textContent = text;
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
