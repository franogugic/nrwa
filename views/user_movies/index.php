<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Moja lista - MovieList</title>
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
        .btn-logout { background: none; border: 1px solid #555; color: #ccc; padding: 5px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .btn-logout:hover { border-color: #e50914; color: #fff; }

        .container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        h1 { font-size: 28px; margin-bottom: 25px; }

        .section-title { font-size: 18px; margin-bottom: 12px; color: #aaa; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { background: #222; padding: 12px; text-align: left; font-size: 13px; color: #aaa; border-bottom: 1px solid #333; }
        td { padding: 12px; border-bottom: 1px solid #222; font-size: 14px; }
        tr:hover td { background: #1a1a1a; }

        .badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-watched { background: #27ae60; color: #fff; }
        .badge-want { background: #e67e22; color: #fff; }

        .empty { color: #666; text-align: center; padding: 40px; }
        .rating { color: #f5c518; }

        .btn-remove, .btn-edit {
            background: none;
            border: 1px solid #555;
            color: #aaa;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-right: 4px;
        }
        .btn-remove:hover { border-color: #e50914; color: #e50914; }
        .btn-edit:hover { border-color: #27ae60; color: #27ae60; }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: #1f1f1f;
            border-radius: 8px;
            padding: 30px;
            width: 400px;
        }
        .modal h3 { margin-bottom: 20px; }
        .modal label { display: block; font-size: 13px; color: #aaa; margin-bottom: 5px; }
        .modal input, .modal select, .modal textarea {
            width: 100%;
            background: #333;
            color: #fff;
            border: 1px solid #555;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .modal textarea { resize: vertical; height: 80px; }
        .modal-buttons { display: flex; gap: 10px; justify-content: flex-end; }
        .btn-save { background: #27ae60; color: #fff; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; }
        .btn-cancel { background: none; border: 1px solid #555; color: #aaa; padding: 8px 20px; border-radius: 4px; cursor: pointer; }

        #auth-msg {
            text-align: center;
            padding: 80px 20px;
            color: #aaa;
            font-size: 16px;
        }
        #auth-msg a { color: #e50914; text-decoration: none; }
        #auth-msg a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <nav>
        <span class="logo">MOVIELIST</span>
        <a href="index.php">Katalog</a>
        <a href="index.php?page=my-list" style="color:#fff;">Moja lista</a>
        <a href="index.php?page=admin" id="nav-admin" style="display:none; color:#f5c518;">Admin</a>
        <div class="nav-right">
            <span id="nav-name" style="color:#aaa; font-size:14px;"></span>
            <button class="btn-logout" onclick="logout()">Odjava</button>
        </div>
    </nav>

    <div class="modal-overlay" id="modal">
        <div class="modal">
            <h3>Uredi zapis</h3>
            <label>Status</label>
            <select id="modal-status">
                <option value="want_to_watch">Želim pogledati</option>
                <option value="watched">Pogledano</option>
            </select>
            <label>Ocjena (1-5)</label>
            <input type="number" id="modal-rating" min="1" max="5" placeholder="Ostavi prazno za bez ocjene">
            <label>Komentar</label>
            <textarea id="modal-comment" placeholder="Tvoj komentar..."></textarea>
            <label>Datum gledanja</label>
            <input type="date" id="modal-date">
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal()">Odustani</button>
                <button class="btn-save" onclick="saveEntry()">Spremi</button>
            </div>
        </div>
    </div>

    <div id="auth-msg" style="display:none;">
        Morate biti <a href="index.php?page=login">prijavljeni</a> kako biste vidjeli svoju listu filmova.
    </div>

    <div id="content" style="display:none;">
        <div class="container">
            <h1>Moja lista filmova</h1>

            <div class="section-title">Pogledano (<span id="cnt-watched">0</span>)</div>
            <div id="watched-section">
                <p class="empty">Učitavanje...</p>
            </div>

            <div class="section-title">Želim pogledati (<span id="cnt-want">0</span>)</div>
            <div id="want-section">
                <p class="empty">Učitavanje...</p>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('ml_token');
        const name  = localStorage.getItem('ml_name');
        const role  = localStorage.getItem('ml_role');

        if (!token) {
            document.getElementById('auth-msg').style.display = 'block';
        } else {
            document.getElementById('content').style.display = 'block';
            document.getElementById('nav-name').textContent = name;
            if (role === 'admin') {
                document.getElementById('nav-admin').style.display = 'inline';
            }
            loadList();
        }

        function loadList() {
            fetch('/movielist/public/api/user-movies', {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(r => r.json())
            .then(entries => {
                if (entries.error) {
                    document.getElementById('watched-section').innerHTML = '<p class="empty">Greška pri dohvatu podataka.</p>';
                    document.getElementById('want-section').innerHTML = '';
                    return;
                }

                const watched = entries.filter(e => e.status === 'watched');
                const want    = entries.filter(e => e.status === 'want_to_watch');

                document.getElementById('cnt-watched').textContent = watched.length;
                document.getElementById('cnt-want').textContent    = want.length;

                document.getElementById('watched-section').innerHTML = watched.length
                    ? buildTable(watched)
                    : '<p class="empty">Nema pogledanih filmova.</p>';

                document.getElementById('want-section').innerHTML = want.length
                    ? buildTable(want)
                    : '<p class="empty">Nema filmova na listi za gledanje.</p>';
            })
            .catch(() => {
                document.getElementById('watched-section').innerHTML = '<p class="empty">Greška pri dohvatu podataka.</p>';
            });
        }

        function buildTable(entries) {
            const rows = entries.map(e => `
                <tr>
                    <td>${e.title}</td>
                    <td>${e.release_year} &bull; ${e.genre}</td>
                    <td>${e.status === 'watched'
                        ? '<span class="badge badge-watched">Pogledano</span>'
                        : '<span class="badge badge-want">Želim pogledati</span>'}</td>
                    <td class="rating">${e.rating !== null ? '★ ' + e.rating + ' / 5' : '-'}</td>
                    <td>${e.comment ? e.comment : '-'}</td>
                    <td>${e.watched_at ? e.watched_at : '-'}</td>
                    <td>
                        <button class="btn-edit" onclick="openModal(${e.id}, ${e.rating ?? 'null'}, '${e.comment ?? ''}', '${e.watched_at ?? ''}', '${e.status}')">Uredi</button>
                        <button class="btn-remove" onclick="removeEntry(${e.id})">Ukloni</button>
                    </td>
                </tr>
            `).join('');

            return `
                <table>
                    <thead>
                        <tr>
                            <th>Film</th>
                            <th>Godina / Žanr</th>
                            <th>Status</th>
                            <th>Ocjena</th>
                            <th>Komentar</th>
                            <th>Datum gledanja</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            `;
        }

        let currentEditId = null;

        function openModal(id, rating, comment, watchedAt, status) {
            currentEditId = id;
            document.getElementById('modal-status').value = status;
            document.getElementById('modal-rating').value = rating !== null ? rating : '';
            document.getElementById('modal-comment').value = comment;
            document.getElementById('modal-date').value = watchedAt;
            document.getElementById('modal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('modal').classList.remove('active');
            currentEditId = null;
        }

        function saveEntry() {
            const status    = document.getElementById('modal-status').value;
            const rating    = document.getElementById('modal-rating').value;
            const comment   = document.getElementById('modal-comment').value;
            const watchedAt = document.getElementById('modal-date').value;

            fetch('/movielist/public/api/user-movies/' + currentEditId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({ status, rating, comment, watched_at: watchedAt })
            })
            .then(r => r.json())
            .then(() => { closeModal(); loadList(); })
            .catch(() => alert('Greška pri spremanju.'));
        }

        function removeEntry(id) {
            if (!confirm('Ukloniti film s liste?')) return;

            fetch('/movielist/public/api/user-movies/' + id, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(r => r.json())
            .then(() => loadList())
            .catch(() => alert('Greška pri uklanjanju.'));
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
