<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Skill;

final class SkillRepository extends TranslatableRepository
{
    protected function table(): string { return 'skills'; }
    protected function translationTable(): string { return 'skill_translations'; }
    protected function foreignKey(): string { return 'skill_id'; }
    protected function columns(): array { return ['category', 'tech', 'mono']; }
    protected function translatedColumns(): array { return ['name']; }
    protected function titleColumn(): string { return 'name'; }
    protected function hasStatus(): bool { return false; }

    /**
     * Compétences regroupées par catégorie, dans l'ordre des cartes de la Stack.
     *
     * @return array<string, list<Skill>> catégorie → compétences (catégories vides absentes)
     */
    public function byCategory(string $lang): array
    {
        $groups = array_fill_keys(Skill::CATEGORIES, []);
        foreach ($this->localizedRows($lang) as $row) {
            $groups[$row['category']][] = Skill::fromRow($row);
        }

        return array_filter($groups);
    }
}
