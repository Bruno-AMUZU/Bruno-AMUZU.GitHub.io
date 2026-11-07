<?php
/**
 * Connexion au back-office. En cas d'erreur : les deux champs en état erreur, un seul message
 * sous le mot de passe (ne dit pas lequel des deux est faux).
 *
 * @var string|null $error
 * @var string|null $notice
 * @var string      $email
 */
?>
<h1 class="auth-card__title t-heading-1"><?= e(t('admin.login.title')) ?></h1>

<?php if (!empty($notice)): ?>
    <p class="auth-card__notice t-small" role="status"><?= e($notice) ?></p>
<?php endif ?>

<form class="auth-card__form" method="post" action="/admin/connexion">
    <?= csrf_field() ?>
    <?= component('field', [
        'name' => 'email', 'type' => 'email', 'label' => t('admin.login.email'), 'value' => $email,
        'required' => true, 'class' => $error ? 'field--error' : null,
        'attributes' => ['autocomplete' => 'username', 'autofocus' => $email === '', 'aria-invalid' => $error ? 'true' : null],
    ]) ?>
    <?= component('field', [
        'name' => 'password', 'type' => 'password', 'label' => t('admin.login.password'),
        'placeholder' => t('admin.login.passwordPlaceholder'), 'required' => true, 'error' => $error,
        'attributes' => ['autocomplete' => 'current-password', 'autofocus' => $email !== ''],
    ]) ?>
    <?= component('button', ['label' => t('admin.login.submit'), 'variant' => 'inverse', 'type' => 'submit', 'full' => true]) ?>
</form>

<a class="auth-card__link t-label" href="/admin/mot-de-passe-oublie"><?= e(t('admin.login.forgot')) ?></a>
