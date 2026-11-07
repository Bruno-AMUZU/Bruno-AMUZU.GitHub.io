<?php
/**
 * ContactBlock — accroche sur fond accent + formulaire (components.md → ContactBlock).
 * Pas de CV, pas de téléphone. État « envoyé » : le formulaire est remplacé par une confirmation.
 *
 * @var string                $action URL d'envoi du formulaire
 * @var bool                  $sent   message envoyé
 * @var array<string, string> $errors champ → message (déjà traduit)
 * @var array<string, string> $old    champ → valeur saisie
 */
$errors ??= [];
$old ??= [];
$types = t_list('form.types');
?>
<section class="contact-block" aria-labelledby="contact-title">
    <div class="contact-block__pitch">
        <h1 class="t-display-l" id="contact-title"><?= e(t('contactPage.title')) ?></h1>
    </div>

    <?php if (!empty($sent)): ?>
        <div class="contact-block__card contact-block__card--sent" role="status">
            <h2 class="t-heading-1"><?= e(t('contactPage.success.title')) ?></h2>
            <p class="t-body muted"><?= e(t('contactPage.success.text')) ?></p>
        </div>
    <?php else: ?>
        <form class="contact-block__card contact-block__form" method="post" action="<?= e($action) ?>" novalidate>
            <?= csrf_field() ?>
            <?php // Champ piège : invisible pour un humain, rempli par les robots (anti-spam). ?>
            <div class="contact-block__trap" aria-hidden="true">
                <label for="f-website">Site web</label>
                <input type="text" id="f-website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="contact-block__row">
                <?= component('field', [
                    'name' => 'name', 'label' => t('form.name'), 'placeholder' => t('form.namePlaceholder'),
                    'value' => $old['name'] ?? '', 'error' => $errors['name'] ?? null, 'required' => true,
                    'attributes' => ['autocomplete' => 'name', 'maxlength' => 120],
                ]) ?>
                <?= component('field', [
                    'name' => 'email', 'type' => 'email', 'label' => t('form.email'), 'placeholder' => t('form.emailPlaceholder'),
                    'value' => $old['email'] ?? '', 'error' => $errors['email'] ?? null, 'required' => true,
                    'attributes' => ['autocomplete' => 'email', 'maxlength' => 190],
                ]) ?>
            </div>

            <?= component('choice-chips', [
                'name' => 'type',
                'legend' => t('form.type'),
                'options' => array_combine(array_keys($types), $types),
                'selected' => $old['type'] ?? '0',
            ]) ?>

            <?= component('field', [
                'name' => 'message', 'type' => 'textarea', 'label' => t('form.message'), 'placeholder' => t('form.messagePlaceholder'),
                'value' => $old['message'] ?? '', 'error' => $errors['message'] ?? null, 'required' => true,
                'attributes' => ['maxlength' => 5000],
            ]) ?>

            <p class="t-caption muted"><?= e(t('form.privacy')) ?></p>

            <div>
                <?= component('button', ['label' => t('form.submit'), 'variant' => 'inverse', 'type' => 'submit']) ?>
            </div>
        </form>
    <?php endif ?>
</section>
