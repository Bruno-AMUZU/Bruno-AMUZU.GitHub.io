<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\Response;

/** Tableau de bord du back-office (design/pages/admin-tableau-de-bord.md). */
final class DashboardController extends AdminController
{
    /** GET /admin */
    public function index(): Response
    {
        return $this->render('admin/dashboard', ['title' => t('admin.dashboard.title')]);
    }
}
