<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Resource\Field;
use App\Controller\Admin\Resource\ResourceController;
use App\Core\Request;
use App\Core\Session;
use App\Core\Uploader;
use App\Model\Repository\CertificationRepository;
use App\Model\Repository\ResourceRepository;

/** Certifications : date, organisme, fichier PDF, lien, ordre ; titre FR/EN. */
final class CertificationAdminController extends ResourceController
{
    public function __construct(Request $request, Session $session, Uploader $uploader, private readonly CertificationRepository $certifications)
    {
        parent::__construct($request, $session, $uploader);
    }

    public static function basePath(): string { return '/admin/certifications'; }
    public static function routeName(): string { return 'admin.certifications'; }
    protected function repository(): ResourceRepository { return $this->certifications; }
    protected function hasStatus(): bool { return false; }

    protected function labels(): array
    {
        return $this->typeLabels('certifications');
    }

    protected function fields(): array
    {
        $f = 'admin.types.certifications.fields.';

        return [
            Field::text('title', t($f . 'title'))->translated()->required()->max(160),
            Field::date('obtained_on', t($f . 'date'))->in('Informations'),
            Field::text('organization', t($f . 'organization'))->required()->max(120)->in('Informations'),
            Field::url('url', t($f . 'url'))->max(300)->help(t($f . 'urlHelp'))->in('Informations'),
            Field::pdf('file_id', t($f . 'file'))->help(t($f . 'fileHelp'))->in('Médias'),
        ];
    }

    protected function defaults(): array
    {
        return [...parent::defaults(), 'organization' => 'OpenClassrooms'];
    }
}
