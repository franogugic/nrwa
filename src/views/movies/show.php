<section class="detail-layout">
    <article class="detail-panel">
        <p class="eyebrow">Detalji filma</p>
        <h1><?= e($movie['naslov']) ?></h1>
        <p class="meta"><?= e($movie['redatelj']) ?> · <?= e((string) $movie['godina']) ?> · <?= e($movie['zanr']) ?></p>
        <p class="description"><?= e($movie['opis']) ?></p>
    </article>

    <aside class="stats-panel">
        <span class="stat-label">Prosjecna ocjena</span>
        <strong><?= $movie['prosjecna_ocjena'] ? e((string) $movie['prosjecna_ocjena']) : '-' ?></strong>
        <span class="stat-label"><?= (int) ($movie['broj_ocjena'] ?? 0) ?> korisnickih ocjena</span>
    </aside>
</section>

<section class="detail-actions">
    <button type="button" data-add-movie data-movie-id="<?= e((string) $movie['id']) ?>" data-status="want_to_watch">
        Dodaj u zelim pogledati
    </button>
    <button type="button" data-add-movie data-movie-id="<?= e((string) $movie['id']) ?>" data-status="watched">
        Dodaj kao pogledano
    </button>
</section>

<a class="back-link" href="/movies">Nazad na katalog</a>
