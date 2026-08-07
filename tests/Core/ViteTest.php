<?php

namespace Themosis\Tests\Core;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\Vite;
use Throwable;

class ViteTest extends TestCase
{
    protected $app;

    public function setUp(): void
    {
        $this->getApplication();
    }

    protected function getApplication()
    {
        if (! is_null($this->app)) {
            return $this->app;
        }

        $app = new Application(dirname(__DIR__));

        $app->singleton('config', function () {
            return new Repository();
        });

        return $this->app = $app;
    }

    public function testProdModeResolvesScriptAndStyleTags()
    {
        $vite = app(Vite::class);

        $this->assertEquals(
            '<script type="module" src="/content/themes/vite-theme/dist/assets/app-4ed993c7.js"></script>'
            . '<link rel="stylesheet" href="/content/themes/vite-theme/dist/assets/app-a1b2c3d4.css">',
            $vite(
                ['resources/js/app.js', 'resources/css/app.css'],
                'content/themes/vite-theme/dist',
            )->toHtml(),
        );
    }

    public function testProdModeResolvesImportsAndCssChunks()
    {
        $vite = app(Vite::class);

        $this->assertEquals(
            '<link rel="modulepreload" href="/content/themes/vite-theme-chunks/dist/assets/vendor-2fbaa5b3.js">'
            . '<link rel="stylesheet" href="/content/themes/vite-theme-chunks/dist/assets/app-a1b2c3d4.css">'
            . '<script type="module" src="/content/themes/vite-theme-chunks/dist/assets/app-4ed993c7.js"></script>',
            $vite('resources/js/app.js', 'content/themes/vite-theme-chunks/dist')->toHtml(),
        );
    }

    public function testDevModeResolvesDevServerTags()
    {
        $vite = app(Vite::class);

        $this->assertEquals(
            '<script type="module" src="http://localhost:5173/@vite/client"></script>'
            . '<script type="module" src="http://localhost:5173/resources/js/app.js"></script>'
            . '<link rel="stylesheet" href="http://localhost:5173/resources/css/app.css">',
            $vite(
                ['resources/js/app.js', 'resources/css/app.css'],
                'content/themes/vite-theme-dev/dist',
            )->toHtml(),
        );
    }

    public function testMissingManifestAlwaysThrows()
    {
        $vite = app(Vite::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The Vite manifest does not exist.');

        $vite('resources/js/app.js', 'content/themes/vite-theme-missing/dist');
    }

    public function testMissingEntryThrowsInDebugMode()
    {
        app('config')->set('app.debug', true);

        $vite = app(Vite::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to locate file in Vite manifest: resources/js/missing.js.');

        $vite(
            ['resources/js/app.js', 'resources/js/missing.js'],
            'content/themes/vite-theme-partial/dist',
        );
    }

    public function testMissingEntryDegradesInProductionMode()
    {
        app('config')->set('app.debug', false);

        app()->singleton(ExceptionHandler::class, function () {
            return new class () implements ExceptionHandler {
                public function report(Throwable $e)
                {
                }

                public function shouldReport(Throwable $e)
                {
                    return false;
                }

                public function render($request, Throwable $e)
                {
                }

                public function renderForConsole($output, Throwable $e)
                {
                }
            };
        });

        $vite = app(Vite::class);

        $this->assertEquals(
            '<script type="module" src="/content/themes/vite-theme-partial/dist/assets/app-4ed993c7.js"></script>',
            $vite(
                ['resources/js/app.js', 'resources/js/missing.js'],
                'content/themes/vite-theme-partial/dist',
            )->toHtml(),
        );
    }
}
