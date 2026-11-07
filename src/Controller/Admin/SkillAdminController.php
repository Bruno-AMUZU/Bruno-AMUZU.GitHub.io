<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Resource\Field;
use App\Controller\Admin\Resource\ResourceController;
use App\Controller\Admin\Resource\ResourceForm;
use App\Core\Request;
use App\Core\Session;
use App\Model\Repository\ResourceRepository;
use App\Model\Repository\SkillRepository;

/** Compétences : catégorie, logo, ordre ; nom FR/EN. */
final class SkillAdminController extends ResourceController
{
    public function __construct(Request $request, Session $session, ResourceForm $form, private readonly SkillRepository $skills)
    {
        parent::__construct($request, $session, $form);
    }

    public static function basePath(): string { return '/admin/competences'; }
    public static function routeName(): string { return 'admin.skills'; }
    protected function repository(): ResourceRepository { return $this->skills; }
    protected function hasStatus(): bool { return false; }
    protected function labels(): array { return $this->typeLabels('skills'); }

    protected function fields(): array
    {
        $f = 'admin.types.skills.fields.';

        // Logos disponibles : fichiers de public/assets/img/tech/.
        $logos = [];
        foreach (glob(ROOT_PATH . '/public/assets/img/tech/*.svg') ?: [] as $file) {
            $key = basename($file, '.svg');
            $logos[$key] = $key;
        }

        return [
            Field::text('name', t($f . 'name'))->translated()->required()->max(60),
            Field::select('category', t($f . 'category'), [
                'front' => t('skills.categories.front'),
                'back' => t('skills.categories.back'),
                'tools' => t('skills.categories.tools'),
                'ai' => t('skills.categories.ai'),
            ])->required()->in('Informations'),
            Field::select('tech', t($f . 'logo'), $logos)->help(t($f . 'logoHelp'))->in('Informations'),
            Field::text('mono', t($f . 'mono'))->max(4)->help(t($f . 'monoHelp'))->in('Informations'),
        ];
    }

    protected function defaults(): array
    {
        return [...parent::defaults(), 'category' => 'front'];
    }
}
