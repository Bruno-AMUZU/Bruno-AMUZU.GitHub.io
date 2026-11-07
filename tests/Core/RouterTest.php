<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Core\Container;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Translator;
use App\Core\UrlGenerator;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->set(Translator::class, fn () => new Translator(ROOT_PATH . '/lang'));
        $this->router = new Router($this->container);
    }

    private function dispatch(string $method, string $path): Response
    {
        return $this->router->dispatch(new Request($method, $path));
    }

    public function testPassesNamedParametersToTheAction(): void
    {
        $this->router->get('/fr/projets/{slug}', fn (string $slug) => "projet {$slug}");

        self::assertSame('projet atelier-brume', $this->dispatch('GET', '/fr/projets/atelier-brume')->body);
    }

    public function testConstraintsLimitAcceptedValues(): void
    {
        $this->router->get('/uploads/{year:\d{4}}', fn (string $year) => $year);

        self::assertSame('2026', $this->dispatch('GET', '/uploads/2026')->body);

        $this->expectExceptionObject(new HttpException(404));
        $this->dispatch('GET', '/uploads/26');
    }

    public function testTrailingSlashIsIgnored(): void
    {
        $this->router->get('/fr/lab', fn () => 'lab');

        self::assertSame('lab', $this->dispatch('GET', '/fr/lab/')->body);
    }

    public function testUnknownPathIs404(): void
    {
        try {
            $this->dispatch('GET', '/nulle-part');
            self::fail('404 attendue');
        } catch (HttpException $e) {
            self::assertSame(404, $e->status);
        }
    }

    public function testWrongMethodIs405WithAllowHeader(): void
    {
        $this->router->get('/fr/contact', fn () => 'form');

        try {
            $this->dispatch('POST', '/fr/contact');
            self::fail('405 attendue');
        } catch (HttpException $e) {
            self::assertSame(405, $e->status);
            self::assertSame('GET', $e->headers['Allow']);
        }
    }

    public function testLocalizedPageSetsLanguageAndBuildsUrls(): void
    {
        $this->router->page('projects', ['fr' => '/fr/projets', 'en' => '/en/projects'], fn (string $lang) => $lang);

        self::assertSame('en', $this->dispatch('GET', '/en/projects')->body);
        self::assertSame('en', $this->container->get(Translator::class)->lang());

        $urls = $this->container->get(UrlGenerator::class);
        self::assertSame('/fr/projets', $urls->switchTo('fr'));
        self::assertSame('/en/projects', $urls->to('projects'));
    }

    public function testMiddlewareCanStopTheRequest(): void
    {
        $this->container->set(BlockEverything::class, fn () => new BlockEverything());
        $this->router->group([BlockEverything::class], function (Router $r): void {
            $r->get('/admin', fn () => 'secret');
        });
        $this->router->get('/public', fn () => 'ok');

        self::assertSame(303, $this->dispatch('GET', '/admin')->status);
        self::assertSame('ok', $this->dispatch('GET', '/public')->body);
    }
}

final class BlockEverything
{
    public function handle(Request $request): ?Response
    {
        return Response::redirect('/login');
    }
}
