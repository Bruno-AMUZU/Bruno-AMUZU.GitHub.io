<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Entrée du parcours : formation ou expérience. */
final class TimelineEntry
{
    public const KINDS = ['education', 'experience'];

    public function __construct(
        public readonly int $id,
        public readonly string $kind,
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly string $organization,
        public readonly string $place,
        public readonly string $title,
        public readonly string $description,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['kind'],
            $row['start_date'] ?: null,
            $row['end_date'] ?: null,
            (string) $row['organization'],
            (string) $row['place'],
            (string) $row['title'],
            (string) $row['description'],
        );
    }

    /** « sept. 2024 — aujourd'hui », « 2023 — 2024 »… dans la langue de la page. */
    public function period(string $lang): string
    {
        if ($this->startDate === null) {
            return '';
        }
        $format = fn (string $date) => format_date($date, $lang, 'month');
        $end = $this->endDate !== null ? $format($this->endDate) : ($lang === 'fr' ? 'aujourd’hui' : 'present');

        return $format($this->startDate) . ' — ' . $end;
    }
}
