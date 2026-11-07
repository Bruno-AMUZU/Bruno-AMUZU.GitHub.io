<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Core\View;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ViewTest extends TestCase
{
    /** La page d'erreur ne dépend d'aucune donnée de la base : elle sert de vue de test. */
    private function renderErrorPage(string $title): string
    {
        return View::render('errors/error', ['title' => $title, 'status' => 404, 'exception' => null]);
    }

    public function testRendersTheViewInsideTheLayout(): void
    {
        $html = $this->renderErrorPage('Essai');

        self::assertStringContainsString('<title>Essai · Bruno Amuzu</title>', $html);
        self::assertStringContainsString('<main', $html);
        self::assertStringContainsString('class="site-header', $html);
    }

    public function testEscapesData(): void
    {
        $html = $this->renderErrorPage('<script>alert(1)</script>');

        self::assertStringNotContainsString('<script>alert(1)</script>', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
    }

    public function testRefusesPathTraversal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        View::render('../.env');
    }

    public function testMissingViewIsAnError(): void
    {
        $this->expectException(RuntimeException::class);
        View::render('site/nexiste-pas');
    }
}
