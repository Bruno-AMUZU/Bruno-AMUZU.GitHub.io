<?php
/**
 * Contact (design/pages/contact.md) : ContactBlock seul. Pas de CV, pas de téléphone.
 *
 * @var bool                  $sent
 * @var array<string, string> $errors
 * @var array<string, string> $old
 */
?>
<div class="container page-top">
    <?= component('contact-block', [
        'action' => url('contact'),
        'sent' => $sent,
        'errors' => $errors,
        'old' => $old,
    ]) ?>
</div>
