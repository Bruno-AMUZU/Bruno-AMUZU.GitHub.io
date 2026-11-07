<?php
/**
 * Mot de passe oublié — demande du lien.
 *
 * @var string|null $error
 */
?>
<h1 class="auth-card__title t-heading-1"><?= e(t('admin.reset.request.title')) ?></h1>
<p class="t-body muted"><?= e(t('admin.reset.request.text')) ?></p>

<form class="auth-card__form" method="post" action="/admin/mot-de-passe-oublie">
    <?= csrf_field() ?>
    <?= component('field', [
        'name' => 'email', 'type' => 'email', 'label' => t('admin.login.email'), 'required' => true, 'error' => $error,
        'attributes' => ['autocomplete' => 'username', 'autofocus' => true],
    ]) ?>
    <?= component('button', ['label' => t('admin.reset.request.submit'), 'variant' => 'inverse', 'type' => 'submit', 'full' => true]) ?>
</form>

<a class="auth-card__link t-label" href="/admin/connexion"><?= e(t('admin.reset.back')) ?></a>
