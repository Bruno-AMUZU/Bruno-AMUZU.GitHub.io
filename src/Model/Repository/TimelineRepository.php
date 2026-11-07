<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\TimelineEntry;

/** Parcours : formations et expériences. */
final class TimelineRepository extends TranslatableRepository
{
    protected function table(): string { return 'timeline_entries'; }
    protected function translationTable(): string { return 'timeline_entry_translations'; }
    protected function foreignKey(): string { return 'timeline_entry_id'; }
    protected function columns(): array { return ['kind', 'start_date', 'end_date', 'organization', 'place']; }
    protected function translatedColumns(): array { return ['title', 'description']; }
    protected function hasStatus(): bool { return false; }

    /** @return list<TimelineEntry> dans l'ordre choisi dans le back-office */
    public function all(string $lang): array
    {
        return array_map(fn (array $row) => TimelineEntry::fromRow($row), $this->localizedRows($lang));
    }
}
