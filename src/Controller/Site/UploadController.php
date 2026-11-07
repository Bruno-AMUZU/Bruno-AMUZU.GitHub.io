<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\HttpException;
use App\Core\Response;
use App\Core\Uploader;
use App\Model\Repository\FileRepository;

/**
 * Sert les fichiers de storage/uploads/ (images des projets, PDF des certifications, CV…).
 * Seuls les fichiers enregistrés en base sont servis, avec leur vrai type.
 */
final class UploadController
{
    public function __construct(
        private readonly FileRepository $files,
        private readonly Uploader $uploader,
    ) {
    }

    /** GET /uploads/{year}/{month}/{name} */
    public function show(string $year, string $month, string $name): Response
    {
        $file = $this->files->findByPath("{$year}/{$month}/{$name}");
        $path = $file ? $this->uploader->absolutePath($file) : null;
        if ($file === null || !is_file((string) $path)) {
            throw new HttpException(404);
        }

        $disposition = $file->isImage() ? 'inline' : 'inline; filename="' . addcslashes($file->originalName, '"\\') . '"';

        return new Response((string) file_get_contents($path), 200, [
            'Content-Type' => $file->mime,
            'Content-Length' => (string) $file->size,
            'Content-Disposition' => $disposition,
            'Cache-Control' => 'public, max-age=31536000, immutable', // nom aléatoire : le contenu ne change jamais
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
