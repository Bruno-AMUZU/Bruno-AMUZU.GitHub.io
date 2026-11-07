<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Resource\Field;
use App\Controller\Admin\Resource\ResourceController;
use App\Controller\Admin\Resource\ResourceForm;
use App\Core\Request;
use App\Core\Session;
use App\Model\Repository\ProjectRepository;
use App\Model\Repository\ResourceRepository;

/** Projets dans le back-office (design/pages/admin-liste.md, admin-edition.md). */
final class ProjectAdminController extends ResourceController
{
    public function __construct(Request $request, Session $session, ResourceForm $form, private readonly ProjectRepository $projects)
    {
        parent::__construct($request, $session, $form);
    }

    public static function basePath(): string { return '/admin/projets'; }
    public static function routeName(): string { return 'admin.projects'; }
    protected function repository(): ResourceRepository { return $this->projects; }

    protected function labels(): array
    {
        return [
            'title' => t('admin.list.title'),
            'back' => t('admin.edit.back'),
            'delete' => t('admin.edit.delete'),
            'empty' => t('admin.list.empty'),
            'newTitle' => t('admin.types.projects.new'),
            'saved' => t('admin.form.saved'),
            'deleted' => t('admin.form.deleted'),
        ];
    }

    protected function fields(): array
    {
        $f = 'admin.edit.fields.';
        $panels = t_list('admin.edit.panels'); // Publication, Informations, Stack, Médias

        return [
            Field::text('title', t($f . 'title'))->translated()->required()->max(120),
            Field::textarea('summary', t($f . 'summary'))->translated()->required()->max(300),
            Field::textarea('context', t($f . 'context'))->translated(),
            Field::textarea('solution', t($f . 'solution'))->translated(),
            Field::textarea('result', t($f . 'result'))->translated(),

            Field::text('slug', t($f . 'slug'))->max(80)->help(t('admin.types.projects.slugHelp'))->in($panels[1]),
            Field::number('year', t($f . 'year'))->in($panels[1]),
            Field::select('type', t($f . 'type'), [
                'site' => t('projects.types.site'),
                'app' => t('projects.types.app'),
                'ia' => t('projects.types.ia'),
            ])->required()->in($panels[1]),
            Field::url('github_url', t($f . 'github'))->max(300)->in($panels[1]),
            Field::url('demo_url', t($f . 'demo'))->max(300)->in($panels[1]),

            Field::tags('technologies', $panels[2])->in($panels[2]),

            Field::image('image_id', t($f . 'image'))->help(t('admin.types.projects.imageHelp'))->in($panels[3]),
            Field::gallery('gallery', t('admin.types.projects.gallery'))->in($panels[3]),
        ];
    }

    protected function defaults(): array
    {
        return [...parent::defaults(), 'type' => 'site', 'year' => (int) date('Y'), 'technologies' => [], 'gallery' => []];
    }

    protected function previewUrl(array $values): ?string
    {
        return has_route('project') && !empty($values['slug'])
            ? url('project', ['slug' => (string) $values['slug']], 'fr') . '?apercu=1'
            : null;
    }
}
