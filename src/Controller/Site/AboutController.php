<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\View;
use App\Model\Entity\Certification;
use App\Model\Repository\CertificationRepository;
use App\Model\Repository\SettingRepository;
use App\Model\Repository\SkillRepository;
use App\Model\Repository\TimelineRepository;

/** À propos (design/pages/a-propos.md) : présentation, parcours, compétences, certifications. */
final class AboutController
{
    public function __construct(
        private readonly SettingRepository $settings,
        private readonly TimelineRepository $timeline,
        private readonly SkillRepository $skills,
        private readonly CertificationRepository $certifications,
    ) {
    }

    /** GET /fr/a-propos, /en/about */
    public function index(string $lang): string
    {
        // Certifications : la plus récente d'abord (celles sans date à la fin).
        $certifications = $this->certifications->all($lang);
        usort($certifications, fn (Certification $a, Certification $b) => strcmp($b->obtainedOn ?? '', $a->obtainedOn ?? ''));

        return View::render('site/about', [
            'title' => t('common.nav.about'),
            'description' => t('about.intro.title'),
            'photo' => $this->settings->fileUrl('photo_file_id'),
            'cv' => $this->settings->fileUrl('cv_file_id'),
            'timeline' => $this->timeline->all($lang),
            'stack' => $this->skills->byCategory($lang),
            'certifications' => $certifications,
        ]);
    }
}
