<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Resource\Field;
use App\Controller\Admin\Resource\ResourceController;
use App\Core\Request;
use App\Core\Session;
use App\Core\Uploader;
use App\Model\Repository\ResourceRepository;
use App\Model\Repository\ServiceRepository;

/** Services : illustration (web / apps / ia), ordre ; titre et description FR/EN. */
final class ServiceAdminController extends ResourceController
{
    public function __construct(Request $request, Session $session, Uploader $uploader, private readonly ServiceRepository $services)
    {
        parent::__construct($request, $session, $uploader);
    }

    public static function basePath(): string { return '/admin/services'; }
    public static function routeName(): string { return 'admin.services'; }
    protected function repository(): ResourceRepository { return $this->services; }
    protected function hasStatus(): bool { return false; }
    protected function labels(): array { return $this->typeLabels('services'); }

    protected function fields(): array
    {
        $f = 'admin.types.services.fields.';

        return [
            Field::text('title', t($f . 'title'))->translated()->required()->max(80),
            Field::textarea('description', t($f . 'description'))->translated()->required()->max(200),
            Field::select('illustration', t($f . 'illustration'), [
                'web' => t($f . 'web'),
                'apps' => t($f . 'apps'),
                'ia' => t($f . 'ia'),
            ])->required()->help(t($f . 'illustrationHelp'))->in('Informations'),
        ];
    }

    protected function defaults(): array
    {
        return [...parent::defaults(), 'illustration' => 'web'];
    }
}
