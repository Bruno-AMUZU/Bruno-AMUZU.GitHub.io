<?php
/**
 * VideoPlayer — vidéo YouTube d'un article (components.md → VideoPlayer), ratio 16:9, fond inv, rayon lg.
 *
 * Aucune requête vers YouTube avant le clic : seule la miniature est affichée. Le clic remplace l'image
 * par le lecteur youtube-nocookie.com (assets/js/video.js). Sans JavaScript, le bouton ouvre YouTube.
 *
 * @var string $id    identifiant YouTube
 * @var string $title titre de l'article (titre accessible du lecteur)
 */
?>
<div class="video-player" data-video="<?= e($id) ?>" data-title="<?= e($title) ?>">
    <img class="video-player__poster" src="https://i.ytimg.com/vi/<?= e($id) ?>/hqdefault.jpg" alt="" loading="lazy">
    <a class="video-player__play" href="https://www.youtube.com/watch?v=<?= e($id) ?>" target="_blank" rel="noopener">
        <?= icon('play', null, 28) ?>
        <span class="sr-only"><?= e(t('articlePage.video.play')) ?> — <?= e($title) ?></span>
    </a>
</div>
