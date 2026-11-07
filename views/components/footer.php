<?php
/**
 * Footer — « © 2026 Bruno Amuzu » à gauche ; à droite, dans cet ordre : GitHub, LinkedIn, X, email,
 * Mentions légales (components.md → Footer). Pas de téléphone. En mobile : copyright puis liens à la ligne.
 */
$links = [];
foreach (t_list('common.footer.links') as $link) {
    $label = (string) ($link['label'] ?? '');
    $href = (string) ($link['url'] ?? '');

    // Les réglages du back-office remplacent les liens par défaut de lang/*.php.
    $href = match (true) {
        $label === 'GitHub' => setting('github_url', $href),
        $label === 'LinkedIn' => setting('linkedin_url', $href),
        $label === 'X' => setting('x_url', $href),
        str_starts_with($href, 'mailto:') => 'mailto:' . setting('contact_email', substr($href, 7)),
        str_contains($href, 'mentions-legales') || str_contains($href, 'legal') => has_route('legal') ? url('legal') : '',
        default => $href,
    };
    if (str_starts_with($href, 'mailto:')) {
        $label = substr($href, 7);
    }
    if ($href !== '') {
        $links[] = ['label' => $label, 'href' => $href, 'external' => str_starts_with($href, 'http')];
    }
}
?>
<footer class="site-footer container">
    <div class="site-footer__inner t-small muted">
        <p><?= e(t('common.footer.copyright')) ?></p>
        <ul class="site-footer__links" role="list">
            <?php foreach ($links as $link): ?>
                <li><a<?= attrs([
                    'href' => $link['href'],
                    'rel' => $link['external'] ? 'noopener' : null,
                    'target' => $link['external'] ? '_blank' : null,
                ]) ?>><?= e($link['label']) ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
</footer>
