<?php

declare(strict_types=1);

namespace App;

use App\Controller\Admin\AuthController;
use App\Controller\Admin\CertificationAdminController;
use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PasswordResetController;
use App\Controller\Admin\ProjectAdminController;
use App\Controller\Admin\SkillAdminController;
use App\Controller\Admin\TimelineAdminController;
use App\Controller\Site\HomeController;
use App\Controller\Site\StyleguideController;
use App\Controller\Site\UploadController;
use App\Core\RequireAdmin;
use App\Core\Response;
use App\Core\Router;

/**
 * Toutes les URL du site, au même endroit.
 * Les pages publiques ont un chemin par langue ; le nom (« home », « projects »…) sert à fabriquer les liens.
 * Le back-office est en français uniquement, sous /admin, réservé aux administrateurs connectés.
 */
final class Routes
{
    public static function register(Router $router): void
    {
        self::site($router);
        self::admin($router);
    }

    private static function site(Router $router): void
    {
        $router->get('/', [HomeController::class, 'root']);
        $router->get('/uploads/{year:\d{4}}/{month:\d{2}}/{name:[a-f0-9]{32}\.[a-z0-9]+}', [UploadController::class, 'show']);

        $router->page('home', ['fr' => '/fr', 'en' => '/en'], [HomeController::class, 'index']);

        // Développement uniquement (404 ailleurs) : galerie des composants.
        $router->page('dev.styleguide', ['fr' => '/dev/composants'], [StyleguideController::class, 'index']);
    }

    private static function admin(Router $router): void
    {
        // Accessibles sans être connecté.
        $router->get('/admin/connexion', [AuthController::class, 'showLogin'], 'admin.login');
        $router->post('/admin/connexion', [AuthController::class, 'login']);
        $router->get('/admin/mot-de-passe-oublie', [PasswordResetController::class, 'showRequest']);
        $router->post('/admin/mot-de-passe-oublie', [PasswordResetController::class, 'sendLink']);
        $router->get('/admin/lien-envoye', [PasswordResetController::class, 'sent']);
        $router->get('/admin/nouveau-mot-de-passe/{token:[a-f0-9]{64}}', [PasswordResetController::class, 'showNew']);
        $router->post('/admin/nouveau-mot-de-passe/{token:[a-f0-9]{64}}', [PasswordResetController::class, 'saveNew']);

        $router->group([RequireAdmin::class], function (Router $router): void {
            $router->post('/admin/deconnexion', [AuthController::class, 'logout'], 'admin.logout');
            $router->get('/admin', [DashboardController::class, 'index'], 'admin.dashboard');
            ProjectAdminController::routes($router);
            CertificationAdminController::routes($router);
            TimelineAdminController::routes($router);
            SkillAdminController::routes($router);
        });
    }
}
