<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Throttle;

/** Connexion et déconnexion du back-office (design/pages/admin-connexion.md). */
final class AuthController extends AdminController
{
    /** 5 essais par adresse et par IP, 20 par IP, sur 15 minutes. */
    private const WINDOW = 900;

    public function __construct(
        private readonly Request $request,
        private readonly Auth $auth,
        private readonly Session $session,
        private readonly Throttle $throttle,
    ) {
    }

    /** GET /admin/connexion */
    public function showLogin(): Response
    {
        if ($this->auth->check()) {
            return $this->redirect('/admin');
        }

        return $this->render('admin/login', [
            'title' => t('admin.login.title'),
            'error' => $this->session->getFlash('login_error'),
            'notice' => $this->session->getFlash('notice'),
            'email' => (string) ($this->session->getFlash('old', [])['email'] ?? ''),
        ], 'admin-auth');
    }

    /** POST /admin/connexion */
    public function login(): Response
    {
        $email = mb_strtolower((string) $this->request->input('email'));
        $password = (string) ($this->request->all()['password'] ?? ''); // pas de trim : un espace peut faire partie du mot de passe
        $ip = $this->request->ip();
        $keys = ['login:ip:' . $ip, 'login:pair:' . $ip . ':' . $email];

        if ($this->throttle->tooMany($keys[0], 20, self::WINDOW) || $this->throttle->tooMany($keys[1], 5, self::WINDOW)) {
            return $this->fail($email, t('admin.login.throttled'));
        }

        $admin = $email !== '' && $password !== '' ? $this->auth->attempt($email, $password) : null;
        if ($admin === null) {
            foreach ($keys as $key) {
                $this->throttle->hit($key);
            }

            return $this->fail($email, t('admin.login.error'));
        }

        $this->throttle->clear($keys[1]);
        $this->auth->login($admin);

        return $this->redirect('/admin');
    }

    /** POST /admin/deconnexion (formulaire avec jeton CSRF : un lien ne suffit pas à déconnecter) */
    public function logout(): Response
    {
        $this->auth->logout();

        return $this->redirect('/admin/connexion');
    }

    private function fail(string $email, string $message): Response
    {
        $this->session->flash('login_error', $message);
        $this->session->flash('old', ['email' => $email]);

        return $this->redirect('/admin/connexion');
    }
}
