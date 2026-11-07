<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\Request;
use App\Core\View;
use App\Model\Entity\Project;
use App\Model\Repository\ProjectRepository;

/** Projets (design/pages/projets.md, projet.md). */
final class ProjectController
{
    public function __construct(
        private readonly Request $request,
        private readonly ProjectRepository $projects,
    ) {
    }

    /** GET /fr/projets, /en/projects ; ?type=site|app|ia filtre la grille (lien, fonctionne sans JavaScript). */
    public function index(string $lang): string
    {
        $type = $this->request->query('type');
        $type = in_array($type, Project::TYPES, true) ? $type : null;

        $labels = t_list('projectsPage.filters'); // Tous, Sites, Applications, IA
        $filters = [['label' => $labels[0] ?? 'Tous', 'href' => url('projects'), 'active' => $type === null]];
        foreach (Project::TYPES as $i => $key) {
            $filters[] = ['label' => $labels[$i + 1] ?? $key, 'href' => url('projects') . '?type=' . $key, 'active' => $type === $key];
        }

        return View::render('site/projects', [
            'title' => t('projectsPage.title'),
            'description' => t('projectsPage.intro'),
            'projects' => $this->projects->published($lang, $type),
            'filters' => $filters,
        ]);
    }
}
