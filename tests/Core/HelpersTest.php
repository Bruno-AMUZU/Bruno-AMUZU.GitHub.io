<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Controller\Site\HomeController;
use App\Core\Translator;
use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testEscapeNeutralisesHtmlButKeepsAccents(): void
    {
        self::assertSame('&lt;b&gt; &amp; l&#039;été &quot;x&quot;', e('<b> & l\'été "x"'));
        self::assertSame('', e(null));
        self::assertSame('42', e(42));
    }

    public function testTranslatorFallsBackToFrenchThenToTheKey(): void
    {
        $t = new Translator(ROOT_PATH . '/lang');
        $t->setLang('en');

        self::assertSame('Projects', $t->get('common.nav.projects'));
        self::assertSame('Voir le site', $t->get('admin.viewSite')); // back-office : français seulement
        self::assertSame('cle.inconnue', $t->get('cle.inconnue'));
    }

    public function testUnknownLanguageFallsBackToFrench(): void
    {
        $t = new Translator(ROOT_PATH . '/lang');
        $t->setLang('de');

        self::assertSame('fr', $t->lang());
    }

    public function testPreferredLanguageFromBrowser(): void
    {
        self::assertSame('en', HomeController::preferredLang('en-US,en;q=0.9,fr;q=0.8'));
        self::assertSame('fr', HomeController::preferredLang('de-DE,de;q=0.9'));
        self::assertSame('fr', HomeController::preferredLang(''));
    }
}
