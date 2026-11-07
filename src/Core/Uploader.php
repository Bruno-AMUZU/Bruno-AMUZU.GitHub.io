<?php

declare(strict_types=1);

namespace App\Core;

use App\Model\Entity\File;
use App\Model\Repository\FileRepository;
use finfo;

/**
 * Reçoit un fichier envoyé par un formulaire, le vérifie et le range dans storage/uploads/.
 *
 * Sécurité :
 * - le type est lu dans le contenu du fichier (finfo), jamais dans son nom ni dans ce que dit le navigateur ;
 * - seuls quelques types sont acceptés (pas de SVG : il peut contenir du JavaScript) ;
 * - le nom sur le disque est aléatoire : impossible de deviner ou d'écraser un fichier ;
 * - storage/ est hors de public/ : un fichier envoyé n'est jamais exécuté par le serveur.
 */
final class Uploader
{
    /** Types acceptés → extension et poids maximum (octets). */
    public const TYPES = [
        'image' => [
            'image/jpeg' => ['jpg', 5_000_000],
            'image/png' => ['png', 5_000_000],
            'image/webp' => ['webp', 5_000_000],
        ],
        'pdf' => [
            'application/pdf' => ['pdf', 10_000_000],
        ],
    ];

    public function __construct(
        private readonly FileRepository $files,
        private readonly string $directory,
    ) {
    }

    /**
     * @param array<string, mixed> $upload élément de $_FILES (Request::file())
     * @param 'image'|'pdf' $kind
     * @throws UploadException avec une clé de message traduisible
     */
    public function store(array $upload, string $kind): File
    {
        $error = (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new UploadException('upload.tooBig');
        }
        $tmp = (string) ($upload['tmp_name'] ?? '');
        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
            throw new UploadException('upload.failed');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp) ?: '';
        $allowed = self::TYPES[$kind] ?? [];
        if (!isset($allowed[$mime])) {
            throw new UploadException('upload.type');
        }

        [$extension, $maxSize] = $allowed[$mime];
        $size = (int) filesize($tmp);
        if ($size > $maxSize) {
            throw new UploadException('upload.tooBig');
        }

        $width = $height = null;
        if ($kind === 'image') {
            $dimensions = @getimagesize($tmp);
            if ($dimensions === false) {
                throw new UploadException('upload.type'); // se dit image mais n'en est pas une
            }
            [$width, $height] = $dimensions;
        }

        $relative = date('Y/m') . '/' . bin2hex(random_bytes(16)) . '.' . $extension;
        $target = $this->directory . '/' . $relative;
        if (!is_dir(dirname($target)) && !mkdir(dirname($target), 0775, true) && !is_dir(dirname($target))) {
            throw new UploadException('upload.failed');
        }
        if (!move_uploaded_file($tmp, $target)) {
            throw new UploadException('upload.failed');
        }

        $name = mb_substr(basename((string) ($upload['name'] ?? 'fichier')), 0, 200);

        return $this->files->create($relative, $name, $mime, $size, $width, $height);
    }

    /** Supprime le fichier du disque et de la base. */
    public function delete(File $file): void
    {
        $path = $this->directory . '/' . $file->path;
        if (is_file($path)) {
            unlink($path);
        }
        $this->files->delete($file->id);
    }

    public function absolutePath(File $file): string
    {
        return $this->directory . '/' . $file->path;
    }
}
