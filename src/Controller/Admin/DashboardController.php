<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\App;
use App\Core\Response;
use App\Model\Repository\MessageRepository;
use App\Model\Repository\ProjectRepository;

/** Tableau de bord du back-office (design/pages/admin-tableau-de-bord.md). */
final class DashboardController extends AdminController
{
    public function __construct(
        private readonly MessageRepository $messages,
        private readonly ProjectRepository $projects,
    ) {
    }

    /** GET /admin */
    public function index(): Response
    {
        // Articles du Lab : comptés seulement une fois le type de contenu disponible (V2.1).
        $articles = class_exists('App\Model\Repository\ArticleRepository')
            ? App::get('App\Model\Repository\ArticleRepository')
            : null;

        return $this->render('admin/dashboard', [
            'title' => t('admin.dashboard.title'),
            'stats' => [
                'unread' => $this->messages->countUnread(),
                'projects' => $this->projects->countByStatus('published'),
                'articles' => $articles?->countByStatus('published') ?? 0,
                'drafts' => $this->projects->countByStatus('draft') + ($articles?->countByStatus('draft') ?? 0),
            ],
            'latest' => $this->messages->list('all', 3),
        ]);
    }
}
