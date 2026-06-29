<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Upravljanje katalogom - MovieList</title>
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
        nav .nav-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .btn-logout { background: none; border: 1px solid #555; color: #ccc; padding: 5px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .btn-logout:hover { border-color: #e50914; color: #fff; }

        .container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        h1 { font-size: 26px; margin-bottom: 8px; }
        .subtitle { color: #aaa; font-size: 14px; margin-bottom: 30px; }

        .form-box {
            background: #1f1f1f;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 40px;
        }
        .form-box h2 { font-size: 18px; margin-bottom: 18px; }

        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .field { margin-bottom: 14px; }
        label { display: block; font-size: 12px; color: #aaa; margin-bottom: 5px; }
        input, textarea, select {
            width: 100%;
            background: #333;
            color: #fff;
            border: 1px solid #555;
            padding: 9px 12px;
            border-radius: 4px;
            font-size: 14px;
        }
        input:focus, textarea:focus { outline: none; border-color: #e50914; }
        textarea { resize: vertical; min-height: 70px; }

        .btn-primary {
            background: #e50914;
            color: #fff;
            border: none;
            padding: 9px 22px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-primary:hover { background: #b00710; }
        .btn-secondary {
            background: #333;
            color: #ccc;
            border: none;
            padding: 9px 22px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin-left: 10px;
        }
        .btn-edit {
            background: #2980b9;
            color: #fff;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }

        #form-msg { margin-top: 12px; font-size: 13px; min-height: 18px; }
        .error { color: #e50914; }
        .success { color: #27ae60; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #222; padding: 11px 12px; text-align: left; font-size: 13px; color: #aaa; border-bottom: 1px solid #333; }
        td { padding: 11px 12px; border-bottom: 1px solid #222; font-size: 13px; }
        tr:hover td { background: #1a1a1a; }

        #access-denied { text-align: center; padding: 80px 20px; color: #e50914; font-size: 20px; }
    </style>
</head>
<body>
    <nav>
        <span class="logo">MOVIELIST</span>
        <a href="index.php">Katalog</a>
        <div class="nav-right">
            <span id="nav-name" style="color:#aaa; font-size:14px;"></span>
            <button class="btn-logout" onclick="logout()">Odjava</button>
        </div>
    </nav>

    <div id="access-denied" style="display:none;">
        Pristup zabranjen. Ova stranica je dostupna samo administratorima.
        <br><br>
        <a href="index.php" style="color:#aaa; font-size:14px;">← Povratak na katalog</a>
    </div>

    <div id="admin-panel" style="display:none;">
        <div class="container">
            <h1>Admin - Katalog filmova</h1>
            <p class="subtitle">Dodavanje i uređivanje filmova u centralnom katalogu.</p>

            <div class="form-box">
                <h2 id="form-title">Dodaj novi film</h2>
                <input type="hidden" id="edit-id">

                <div class="row">
                    <div class="field">
                        <label>Naslov *</label>
                        <input type="text" id="f-title" placeholder="Naziv filma">
                    </div>
                    <div class="field">
                        <label>Redatelj *</label>
                        <input type="text" id="f-director" placeholder="Ime redatelja">
                    </div>
                </div>
                <div class="row">
                    <div class="field">
                        <label>Godina *</label>
                        <input type="number" id="f-year" placeholder="2024" min="1888" max="2100">
                    </div>
                    <div class="field">
                        <label>Žanr *</label>
                        <input type="text" id="f-genre" placeholder="Drama, Action...">
                    </div>
                </div>
                <div class="field">
                    <label>Opis</label>
                    <textarea id="f-desc" placeholder="Kratki opis filma..."></textarea>
                </div>

                <button class="btn-primary" onclick="submitForm()">Spremi film</button>
                <button class="btn-secondary" id="btn-cancel" onclick="cancelEdit()" style="display:none;">Odustani</button>
                <div id="form-msg"></div>
            </div>

            <h2 style="font-size:18px; margin-bottom:16px;">Svi filmovi u katalogu</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naslov</th>
                        <th>Redatelj</th>
                        <th>Godina</th>
                        <th>Žanr</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="movies-tbody">
                    <tr><td colspan="6" style="text-align:center; color:#666;">Učitavanje...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('ml_token');
        const role  = localStorage.getItem('ml_role');
        const name  = localStorage.getItem('ml_name');

        if (!token || role !== 'admin') {
            document.getElementById('access-denied').style.display = 'block';
        } else {
            document.getElementById('admin-panel').style.display = 'block';
            document.getElementById('nav-name').textContent = name;
            loadMovies();
        }

        function authHeaders() {
            return { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token };
        }

        function loadMovies() {
            fetch('/movielist/public/index.php/api/movies')
                .then(r => r.json())
                .then(movies => {
                    const tbody = document.getElementById('movies-tbody');
                    if (!movies.length) {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#666;">Nema filmova.</td></tr>';
                        return;
                    }
                    tbody.innerHTML = movies.map(m => `
                        <tr>
                            <td>${m.id}</td>
                            <td>${m.title}</td>
                            <td>${m.director}</td>
                            <td>${m.release_year}</td>
                            <td>${m.genre}</td>
                            <td><button class="btn-edit" onclick="startEdit(${m.id},'${escJs(m.title)}','${escJs(m.director)}',${m.release_year},'${escJs(m.genre)}','${escJs(m.description||'')}')">Uredi</button></td>
                        </tr>
                    `).join('');
                });
        }

        function escJs(str) {
            return (str || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/\n/g, ' ');
        }

        function submitForm() {
            const editId = document.getElementById('edit-id').value;
            const body = {
                title:        document.getElementById('f-title').value.trim(),
                director:     document.getElementById('f-director').value.trim(),
                release_year: parseInt(document.getElementById('f-year').value),
                genre:        document.getElementById('f-genre').value.trim(),
                description:  document.getElementById('f-desc').value.trim(),
            };
            const msg    = document.getElementById('form-msg');
            const isEdit = editId !== '';
            const url    = isEdit
                ? '/movielist/public/index.php/api/admin/movies/' + editId
                : '/movielist/public/index.php/api/admin/movies';

            fetch(url, {
                method: isEdit ? 'PUT' : 'POST',
                headers: authHeaders(),
                body: JSON.stringify(body)
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    msg.className = 'error';
                    msg.textContent = data.error;
                    return;
                }
                msg.className = 'success';
                msg.textContent = data.message;
                cancelEdit();
                loadMovies();
            })
            .catch(() => { msg.className = 'error'; msg.textContent = 'Greška pri slanju zahtjeva.'; });
        }

        function startEdit(id, title, director, year, genre, desc) {
            document.getElementById('edit-id').value = id;
            document.getElementById('f-title').value = title;
            document.getElementById('f-director').value = director;
            document.getElementById('f-year').value = year;
            document.getElementById('f-genre').value = genre;
            document.getElementById('f-desc').value = desc;
            document.getElementById('form-title').textContent = 'Uredi film';
            document.getElementById('btn-cancel').style.display = 'inline-block';
            document.getElementById('form-msg').textContent = '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function cancelEdit() {
            document.getElementById('edit-id').value = '';
            document.getElementById('f-title').value = '';
            document.getElementById('f-director').value = '';
            document.getElementById('f-year').value = '';
            document.getElementById('f-genre').value = '';
            document.getElementById('f-desc').value = '';
            document.getElementById('form-title').textContent = 'Dodaj novi film';
            document.getElementById('btn-cancel').style.display = 'none';
            document.getElementById('form-msg').textContent = '';
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
