<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Service;

final class ServiceRepository extends TranslatableRepository
{
    protected function table(): string { return 'services'; }
    protected function translationTable(): string { return 'service_translations'; }
    protected function foreignKey(): string { return 'service_id'; }
    protected function columns(): array { return ['illustration']; }
    protected function translatedColumns(): array { return ['title', 'description']; }
    protected function hasStatus(): bool { return false; }

    /** @return list<Service> dans l'ordre choisi */
    public function all(string $lang): array
    {
        return array_map(fn (array $row) => Service::fromRow($row), $this->localizedRows($lang));
    }
}
