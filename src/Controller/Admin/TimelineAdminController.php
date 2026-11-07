<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Resource\Field;
use App\Controller\Admin\Resource\ResourceController;
use App\Core\Request;
use App\Core\Session;
use App\Core\Uploader;
use App\Model\Repository\ResourceRepository;
use App\Model\Repository\TimelineRepository;

/** Parcours : type, début, fin, structure, lieu, ordre ; intitulé et description FR/EN. */
final class TimelineAdminController extends ResourceController
{
    public function __construct(Request $request, Session $session, Uploader $uploader, private readonly TimelineRepository $timeline)
    {
        parent::__construct($request, $session, $uploader);
    }

    public static function basePath(): string { return '/admin/parcours'; }
    public static function routeName(): string { return 'admin.timeline'; }
    protected function repository(): ResourceRepository { return $this->timeline; }
    protected function hasStatus(): bool { return false; }
    protected function labels(): array { return $this->typeLabels('timeline'); }

    protected function fields(): array
    {
        $f = 'admin.types.timeline.fields.';

        return [
            Field::text('title', t($f . 'title'))->translated()->required()->max(160),
            Field::textarea('description', t($f . 'description'))->translated()->max(800),
            Field::select('kind', t($f . 'kind'), [
                'education' => t('about.kinds.education'),
                'experience' => t('about.kinds.experience'),
            ])->required()->in('Informations'),
            Field::month('start_date', t($f . 'start'))->in('Informations'),
            Field::month('end_date', t($f . 'end'))->help(t($f . 'endHelp'))->in('Informations'),
            Field::text('organization', t($f . 'organization'))->max(120)->in('Informations'),
            Field::text('place', t($f . 'place'))->max(120)->in('Informations'),
        ];
    }

    protected function extraErrors(array $values, ?int $id): array
    {
        if (!empty($values['start_date']) && !empty($values['end_date']) && $values['end_date'] < $values['start_date']) {
            return ['end_date' => t('admin.types.timeline.fields.endBeforeStart')];
        }

        return [];
    }

    protected function defaults(): array
    {
        return [...parent::defaults(), 'kind' => 'education'];
    }
}
