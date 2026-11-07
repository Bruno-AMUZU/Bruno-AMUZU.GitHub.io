<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Resource\Field;
use App\Controller\Admin\Resource\ResourceController;
use App\Controller\Admin\Resource\ResourceForm;
use App\Core\Request;
use App\Core\Session;
use App\Model\Entity\Article;
use App\Model\Repository\ArticleRepository;
use App\Model\Repository\ResourceRepository;

/** Articles du Lab : slug, catégorie, date, temps de lecture, statut, vidéo YouTube ; titre, extrait, contenu FR/EN. */
final class ArticleAdminController extends ResourceController
{
    public function __construct(Request $request, Session $session, ResourceForm $form, private readonly ArticleRepository $articles)
    {
        parent::__construct($request, $session, $form);
    }

    public static function basePath(): string { return '/admin/lab'; }
    public static function routeName(): string { return 'admin.lab'; }
    protected function repository(): ResourceRepository { return $this->articles; }
    protected function labels(): array { return $this->typeLabels('articles'); }

    protected function fields(): array
    {
        $f = 'admin.types.articles.fields.';
        $categories = array_slice(t_list('labPage.filters'), 1); // sans « Tout »

        return [
            Field::text('title', t($f . 'title'))->translated()->required()->max(140),
            Field::textarea('excerpt', t($f . 'excerpt'))->translated()->required()->max(300),
            Field::markdown('content', t($f . 'content'))->translated()->help(t($f . 'contentHelp')),

            Field::text('slug', t('admin.edit.fields.slug'))->max(80)->help(t('admin.types.projects.slugHelp'))->in('Informations'),
            Field::select('category', t($f . 'category'), array_combine(Article::CATEGORIES, $categories))->required()->in('Informations'),
            Field::date('published_on', t($f . 'date'))->in('Informations'),
            Field::number('reading_time', t($f . 'readingTime'))->in('Informations'),
            Field::url('video_url', t($f . 'video'))->max(300)->help(t($f . 'videoHelp'))->in('Vidéo'),
        ];
    }

    protected function extraErrors(array $values, ?int $id): array
    {
        $errors = [];
        if (!empty($values['video_url']) && Article::parseYoutubeId((string) $values['video_url']) === null) {
            $errors['video_url'] = t('admin.types.articles.fields.videoInvalid');
        }
        if (isset($values['reading_time']) && is_int($values['reading_time']) && ($values['reading_time'] < 1 || $values['reading_time'] > 180)) {
            $errors['reading_time'] = t('admin.types.articles.fields.readingTimeInvalid');
        }

        return $errors;
    }

    protected function defaults(): array
    {
        return [...parent::defaults(), 'category' => 'back', 'published_on' => date('Y-m-d')];
    }

    protected function previewUrl(array $values): ?string
    {
        return has_route('article') && !empty($values['slug'])
            ? url('article', ['slug' => (string) $values['slug']], 'fr') . '?apercu=1'
            : null;
    }
}
