<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Prijava - MovieList</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #141414; color: #fff; min-height: 100vh; display: flex; flex-direction: column; }

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

        .center { flex: 1; display: flex; align-items: center; justify-content: center; }

        .form-box {
            background: #1f1f1f;
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
        }
        h2 { font-size: 24px; margin-bottom: 24px; }

        .field { margin-bottom: 18px; }
        label { display: block; font-size: 13px; color: #aaa; margin-bottom: 6px; }
        input {
            width: 100%;
            background: #333;
            color: #fff;
            border: 1px solid #555;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 14px;
        }
        input:focus { outline: none; border-color: #e50914; }

        button {
            width: 100%;
            background: #e50914;
            color: #fff;
            border: none;
            padding: 11px;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 6px;
        }
        button:hover { background: #b00710; }

        #msg { margin-top: 14px; font-size: 13px; min-height: 18px; }
        .error { color: #e50914; }
        .success { color: #27ae60; }

        .switch { margin-top: 18px; font-size: 13px; color: #aaa; text-align: center; }
        .switch a { color: #e50914; text-decoration: none; }
        .switch a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <nav>
        <span class="logo">MOVIELIST</span>
        <a href="index.php">Katalog</a>
    </nav>

    <div class="center">
        <div class="form-box">
            <h2>Prijava</h2>

            <div class="field">
                <label>Email</label>
                <input type="email" id="email" placeholder="vas@email.com">
            </div>
            <div class="field">
                <label>Lozinka</label>
                <input type="password" id="password" placeholder="••••••">
            </div>

            <button onclick="login()">Prijavi se</button>
            <div id="msg"></div>

            <div class="switch">Nemaš račun? <a href="index.php?page=register">Registracija</a></div>
        </div>
    </div>

    <script>
        function login() {
            const email    = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const msg      = document.getElementById('msg');

            msg.textContent = '';

            fetch('/movielist/public/index.php/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    msg.className = 'error';
                    msg.textContent = data.error;
                    return;
                }
                localStorage.setItem('ml_token', data.token);
                localStorage.setItem('ml_name', data.name);
                localStorage.setItem('ml_role', data.role);
                msg.className = 'success';
                msg.textContent = 'Uspješna prijava! Preusmjeravanje...';
                setTimeout(() => { window.location.href = 'index.php'; }, 800);
            })
            .catch(() => {
                msg.className = 'error';
                msg.textContent = 'Greška pri komunikaciji s API-jem.';
            });
        }

        document.addEventListener('keydown', e => { if (e.key === 'Enter') login(); });
    </script>
</body>
</html>
