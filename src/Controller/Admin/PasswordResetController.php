<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\Env;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Throttle;
use App\Model\Repository\AdminRepository;
use App\Model\Repository\PasswordResetRepository;

/**
 * Récupération du mot de passe par lien e-mail (design/pages/admin-connexion.md → Récupération).
 * Écrans : demande → lien envoyé → nouveau mot de passe. Le message de confirmation est neutre :
 * il ne dit jamais si l'adresse correspond à un compte.
 */
final class PasswordResetController extends AdminController
{
    public const MIN_LENGTH = 12;

    public function __construct(
        private readonly Request $request,
        private readonly Session $session,
        private readonly Throttle $throttle,
        private readonly AdminRepository $admins,
        private readonly PasswordResetRepository $resets,
        private readonly Mailer $mailer,
    ) {
    }

    /** GET /admin/mot-de-passe-oublie */
    public function showRequest(): Response
    {
        return $this->render('admin/reset-request', [
            'title' => t('admin.reset.request.title'),
            'error' => $this->session->getFlash('reset_error'),
        ], 'admin-auth');
    }

    /** POST /admin/mot-de-passe-oublie */
    public function sendLink(): Response
    {
        $email = mb_strtolower((string) $this->request->input('email'));
        $ip = $this->request->ip();

        // 5 demandes par heure et par IP, 3 par adresse : limite le spam de la boîte de l'admin.
        if ($this->throttle->tooMany('reset:ip:' . $ip, 5, 3600) || $this->throttle->tooMany('reset:email:' . $email, 3, 3600)) {
            $this->session->flash('reset_error', t('admin.login.throttled'));

            return $this->redirect('/admin/mot-de-passe-oublie');
        }
        $this->throttle->hit('reset:ip:' . $ip);
        $this->throttle->hit('reset:email:' . $email);

        $admin = filter_var($email, FILTER_VALIDATE_EMAIL) ? $this->admins->findByEmail($email) : null;
        if ($admin !== null) {
            $token = $this->resets->create($admin->id);
            $link = rtrim(Env::get('APP_URL', '') ?? '', '/') . '/admin/nouveau-mot-de-passe/' . $token;
            $this->mailer->send($admin->email, t('admin.reset.mail.subject'), t('admin.reset.mail.body', [
                'link' => $link,
                'minutes' => PasswordResetRepository::LIFETIME_MINUTES,
            ]));
        }

        return $this->redirect('/admin/lien-envoye');
    }

    /** GET /admin/lien-envoye */
    public function sent(): Response
    {
        return $this->render('admin/reset-sent', ['title' => t('admin.reset.sent.title')], 'admin-auth');
    }

    /** GET /admin/nouveau-mot-de-passe/{token} */
    public function showNew(string $token): Response
    {
        $valid = $this->resets->adminIdFor($token) !== null;

        return $this->render('admin/reset-new', [
            'title' => t('admin.reset.new.title'),
            'token' => $token,
            'valid' => $valid,
            'errors' => $this->session->getFlash('errors', []),
        ], 'admin-auth', $valid ? 200 : 410);
    }

    /** POST /admin/nouveau-mot-de-passe/{token} */
    public function saveNew(string $token): Response
    {
        $adminId = $this->resets->adminIdFor($token);
        if ($adminId === null) {
            return $this->redirect('/admin/nouveau-mot-de-passe/' . $token);
        }

        $password = (string) ($this->request->all()['password'] ?? '');
        $confirm = (string) ($this->request->all()['password_confirm'] ?? '');
        $errors = [];
        if (mb_strlen($password) < self::MIN_LENGTH) {
            $errors['password'] = t('admin.reset.new.tooShort', ['n' => self::MIN_LENGTH]);
        } elseif ($password !== $confirm) {
            $errors['password_confirm'] = t('admin.reset.new.mismatch');
        }
        if ($errors !== []) {
            $this->session->flash('errors', $errors);

            return $this->redirect('/admin/nouveau-mot-de-passe/' . $token);
        }

        $this->admins->updatePassword($adminId, $password);
        $this->resets->consume($token);
        $this->session->flash('notice', t('admin.reset.new.done'));

        return $this->redirect('/admin/connexion');
    }
}
