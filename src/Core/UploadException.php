<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/** Fichier refusé ; le message est une clé de traduction (upload.type, upload.tooBig…). */
final class UploadException extends RuntimeException
{
}
