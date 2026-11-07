<?php

declare(strict_types=1);

namespace App\Model\Repository;

/**
 * Ce qu'un type de contenu doit savoir faire pour être géré par le back-office générique.
 *
 * Les valeurs d'un formulaire sont un tableau : champs communs à la racine, champs traduits dans
 * 'translations' => ['fr' => [...], 'en' => [...]].
 */
interface ResourceRepository
{
    /**
     * Lignes de la liste du back-office.
     *
     * @return list<array{id: int, title: string, thumb: ?string, langs: list<string>, status: ?string, updated_at: ?string}>
     */
    public function adminList(?string $search, ?string $status): array;

    /** @return array<string, mixed>|null valeurs du formulaire d'édition */
    public function formValues(int $id): ?array;

    /** @param array<string, mixed> $values @return int id enregistré */
    public function save(?int $id, array $values): int;

    /** @return list<int> id des fichiers qui ne servent plus (à supprimer du disque) */
    public function delete(int $id): array;

    /** @param list<int> $ids nouvel ordre */
    public function reorder(array $ids): void;

    /** Le slug est-il déjà pris par un autre contenu ? */
    public function slugTaken(string $slug, ?int $exceptId): bool;
}
