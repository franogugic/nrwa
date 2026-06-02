<section class="page-heading">
    <div>
        <p class="eyebrow">Demo korisnik</p>
        <h1>Moja lista filmova</h1>
    </div>
</section>

<?php
$watched = array_filter($records, fn($record) => $record['status'] === 'watched');
$planned = array_filter($records, fn($record) => $record['status'] === 'want_to_watch');
?>

<section class="list-columns">
    <div>
        <h2>Pogledano</h2>
        <?php foreach ($watched as $record): ?>
            <article class="list-item">
                <h3><?= e($record['naslov']) ?></h3>
                <p class="meta"><?= e($record['redatelj']) ?> · <?= e((string) $record['godina']) ?></p>
                <p>Ocjena: <strong><?= e((string) $record['ocjena']) ?>/5</strong></p>
                <p><?= e($record['komentar']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>

    <div>
        <h2>Zelim pogledati</h2>
        <?php foreach ($planned as $record): ?>
            <article class="list-item">
                <h3><?= e($record['naslov']) ?></h3>
                <p class="meta"><?= e($record['redatelj']) ?> · <?= e((string) $record['godina']) ?></p>
                <p><?= e($record['komentar']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php if (count($records) === 0): ?>
    <p class="empty-state">Demo korisnik jos nema filmova u osobnoj listi.</p>
<?php endif; ?>
