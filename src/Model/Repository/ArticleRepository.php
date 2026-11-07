<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;

/** Articles du Lab : back-office (via TranslatableRepository) et pages du site. */
final class ArticleRepository extends TranslatableRepository
{
    protected function table(): string { return 'articles'; }
    protected function translationTable(): string { return 'article_translations'; }
    protected function foreignKey(): string { return 'article_id'; }
    protected function columns(): array { return ['slug', 'category', 'published_on', 'reading_time', 'video_url']; }
    protected function translatedColumns(): array { return ['title', 'excerpt', 'content']; }

    /** @return list<Article> publiés, du plus récent au plus ancien, éventuellement d'une catégorie */
    public function published(string $lang, ?string $category = null, ?int $limit = null): array
    {
        $where = "m.status = 'published'" . ($category !== null ? ' AND m.category = :category' : '');
        $rows = $this->localizedRows($lang, $where, $category !== null ? ['category' => $category] : []);
        $articles = array_map(fn (array $row) => Article::fromRow($row), $rows);

        // Le Lab se lit du plus récent au plus ancien (les articles sans date à la fin).
        usort($articles, fn (Article $a, Article $b) => strcmp($b->publishedOn ?? '', $a->publishedOn ?? ''));

        return $limit !== null ? array_slice($articles, 0, $limit) : $articles;
    }

    /** Article par son slug ; un brouillon n'est renvoyé que si $withDrafts (aperçu du back-office). */
    public function findBySlug(string $slug, string $lang, bool $withDrafts = false): ?Article
    {
        $where = 'm.slug = :slug' . ($withDrafts ? '' : " AND m.status = 'published'");
        $rows = $this->localizedRows($lang, $where, ['slug' => $slug]);

        return $rows !== [] ? Article::fromRow($rows[0]) : null;
    }

    /** @return list<Article> deux articles liés : même catégorie d'abord, puis les plus récents */
    public function related(Article $article, string $lang, int $count = 2): array
    {
        $others = array_values(array_filter($this->published($lang), fn (Article $a) => $a->id !== $article->id));
        usort($others, fn (Article $a, Article $b) => ($b->category === $article->category) <=> ($a->category === $article->category));

        return array_slice($others, 0, $count);
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare('SELECT count(*) FROM articles WHERE status = :status');
        $stmt->execute(['status' => $status]);

        return (int) $stmt->fetchColumn();
    }
}
