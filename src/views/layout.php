<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'MovieList') ?> | MovieList</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="/">MovieList</a>
        <nav class="nav">
            <a href="/movies">Katalog</a>
            <a href="/my-list">Moja lista</a>
        </nav>
    </header>

    <main class="page">
        <?= $content ?>
    </main>

    <script src="/app.js"></script>
</body>
</html>
