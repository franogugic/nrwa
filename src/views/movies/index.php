<section class="page-heading">
    <div>
        <p class="eyebrow">Centralni katalog</p>
        <h1>Katalog filmova</h1>
    </div>
    <form class="search-form" method="get" action="/movies">
        <label for="search">Pretraga</label>
        <div class="search-row">
            <input id="search" name="search" type="search" value="<?= e($search) ?>" placeholder="Naslov, redatelj ili zanr">
            <button type="submit">Trazi</button>
        </div>
    </form>
</section>

<?php if ($search !== ''): ?>
    <p class="result-note">Rezultati pretrage za: <strong><?= e($search) ?></strong></p>
<?php endif; ?>

<section class="movie-grid">
    <?php foreach ($movies as $movie): ?>
        <article class="movie-card">
            <div class="movie-card-header">
                <h2><?= e($movie['naslov']) ?></h2>
                <span><?= e((string) $movie['godina']) ?></span>
            </div>
            <p class="meta"><?= e($movie['redatelj']) ?> · <?= e($movie['zanr']) ?></p>
            <p><?= e($movie['opis']) ?></p>
            <div class="card-footer">
                <span class="rating">
                    <?= $movie['prosjecna_ocjena'] ? e((string) $movie['prosjecna_ocjena']) . '/5' : 'Nema ocjena' ?>
                </span>
                <a href="/movies/<?= e((string) $movie['id']) ?>">Detalji</a>
            </div>
            <div class="quick-actions">
                <button type="button" data-add-movie data-movie-id="<?= e((string) $movie['id']) ?>" data-status="want_to_watch">
                    Zelim pogledati
                </button>
                <button type="button" data-add-movie data-movie-id="<?= e((string) $movie['id']) ?>" data-status="watched">
                    Pogledano
                </button>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<?php if (count($movies) === 0): ?>
    <p class="empty-state">Nema filmova za prikaz.</p>
<?php endif; ?>
