<?php

namespace Themosis\Tests\Core;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Console\VendorPublishCommand;

class VendorPublishDirectoryTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/themosis-vendor-publish-' . uniqid();
        mkdir($this->root . '/from/nested', 0755, true);
        file_put_contents($this->root . '/from/a.txt', 'source a');
        file_put_contents($this->root . '/from/nested/b.txt', 'source b');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->deleteDirectory($this->root);
    }

    private function command(bool $force): VendorPublishCommand
    {
        return new class(new Filesystem(), $force) extends VendorPublishCommand {
            public function __construct(Filesystem $files, private bool $force)
            {
                parent::__construct($files);
            }

            public function option($key = null)
            {
                return $key === 'force' ? $this->force : null;
            }

            protected function status($from, $to, $type)
            {
                // Silence console output in tests.
            }

            public function publish(string $from, string $to): void
            {
                $this->publishDirectory($from, $to);
            }
        };
    }

    public function testPublishesEveryFileIncludingNestedOnes()
    {
        $this->command(false)->publish($this->root . '/from', $this->root . '/to');

        $this->assertSame('source a', file_get_contents($this->root . '/to/a.txt'));
        $this->assertSame('source b', file_get_contents($this->root . '/to/nested/b.txt'));
    }

    public function testKeepsExistingFilesUnlessForced()
    {
        mkdir($this->root . '/to', 0755, true);
        file_put_contents($this->root . '/to/a.txt', 'local edit');

        $this->command(false)->publish($this->root . '/from', $this->root . '/to');
        $this->assertSame('local edit', file_get_contents($this->root . '/to/a.txt'));

        $this->command(true)->publish($this->root . '/from', $this->root . '/to');
        $this->assertSame('source a', file_get_contents($this->root . '/to/a.txt'));
    }
}
