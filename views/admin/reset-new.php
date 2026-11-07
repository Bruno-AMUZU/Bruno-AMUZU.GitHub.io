<?php
/**
 * Mot de passe oublié — choix du nouveau mot de passe.
 *
 * @var string                $token
 * @var bool                  $valid  le lien est encore utilisable
 * @var array<string, string> $errors
 */
?>
<h1 class="auth-card__title t-heading-1"><?= e(t('admin.reset.new.title')) ?></h1>

<?php if (!$valid): ?>
    <p class="t-body muted"><?= e(t('admin.reset.new.invalid')) ?></p>
    <a class="auth-card__link t-label" href="/admin/mot-de-passe-oublie"><?= e(t('admin.reset.request.submit')) ?></a>
<?php else: ?>
    <form class="auth-card__form" method="post" action="/admin/nouveau-mot-de-passe/<?= e($token) ?>">
        <?= csrf_field() ?>
        <?= component('field', [
            'name' => 'password', 'type' => 'password', 'label' => t('admin.reset.new.password'), 'required' => true,
            'error' => $errors['password'] ?? null, 'help' => t('admin.reset.new.tooShort', ['n' => App\Controller\Admin\PasswordResetController::MIN_LENGTH]),
            'attributes' => ['autocomplete' => 'new-password', 'minlength' => App\Controller\Admin\PasswordResetController::MIN_LENGTH, 'autofocus' => true],
        ]) ?>
        <?= component('field', [
            'name' => 'password_confirm', 'type' => 'password', 'label' => t('admin.reset.new.confirm'), 'required' => true,
            'error' => $errors['password_confirm'] ?? null,
            'attributes' => ['autocomplete' => 'new-password'],
        ]) ?>
        <?= component('button', ['label' => t('admin.reset.new.submit'), 'variant' => 'inverse', 'type' => 'submit', 'full' => true]) ?>
    </form>
<?php endif ?>
