<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\Response;
use App\Core\View;

/**
 * Base des contrôleurs du back-office : pages jamais mises en cache ni indexées par les moteurs.
 */
abstract class AdminController
{
    /** @param array<string, mixed> $data */
    protected function render(string $view, array $data = [], string $layout = 'admin', int $status = 200): Response
    {
        return $this->private(Response::html(View::render($view, $data, $layout), $status));
    }

    protected function redirect(string $url): Response
    {
        return $this->private(Response::redirect($url));
    }

    private function private(Response $response): Response
    {
        return $response
            ->withHeader('Cache-Control', 'no-store')
            ->withHeader('X-Robots-Tag', 'noindex, nofollow');
    }
}
