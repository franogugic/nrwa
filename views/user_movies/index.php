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

        .container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        h1 { font-size: 28px; margin-bottom: 25px; }

        .tabs { display: flex; gap: 10px; margin-bottom: 25px; }
        .tab {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .tab-watched { background: #27ae60; color: #fff; }
        .tab-want { background: #e67e22; color: #fff; }

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
    </style>
</head>
<body>
    <nav>
        <span class="logo">MOVIELIST</span>
        <a href="index.php">Katalog</a>
        <a href="index.php?page=my-list">Moja lista</a>
    </nav>

    <div class="container">
        <h1>Moja lista filmova</h1>

        <?php
        $watched   = array_filter($entries, fn($e) => $e['status'] === 'watched');
        $wantWatch = array_filter($entries, fn($e) => $e['status'] === 'want_to_watch');
        ?>

        <div class="section-title">Pogledano (<?= count($watched) ?>)</div>
        <?php if (empty($watched)): ?>
            <p class="empty">Nema pogledanih filmova.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Film ID</th>
                        <th>Status</th>
                        <th>Ocjena</th>
                        <th>Komentar</th>
                        <th>Datum gledanja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($watched as $entry): ?>
                        <tr>
                            <td><?= (int) $entry['movie_id'] ?></td>
                            <td><span class="badge badge-watched">Pogledano</span></td>
                            <td class="rating"><?= $entry['rating'] !== null ? '★ ' . (int) $entry['rating'] . ' / 5' : '-' ?></td>
                            <td><?= htmlspecialchars($entry['comment'] ?? '') ?></td>
                            <td><?= htmlspecialchars($entry['watched_at'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="section-title">Želim pogledati (<?= count($wantWatch) ?>)</div>
        <?php if (empty($wantWatch)): ?>
            <p class="empty">Nema filmova na listi za gledanje.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Film ID</th>
                        <th>Status</th>
                        <th>Ocjena</th>
                        <th>Komentar</th>
                        <th>Datum gledanja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wantWatch as $entry): ?>
                        <tr>
                            <td><?= (int) $entry['movie_id'] ?></td>
                            <td><span class="badge badge-want">Želim pogledati</span></td>
                            <td>-</td>
                            <td><?= htmlspecialchars($entry['comment'] ?? '') ?></td>
                            <td>-</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
