<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Core\View;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ViewTest extends TestCase
{
    public function testRendersTheViewInsideTheLayout(): void
    {
        $html = View::render('site/home', ['title' => 'Essai']);

        self::assertStringContainsString('<title>Essai · Bruno Amuzu</title>', $html);
        self::assertStringContainsString('<main', $html);
    }

    public function testEscapesData(): void
    {
        $html = View::render('site/home', ['title' => '<script>alert(1)</script>']);

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
