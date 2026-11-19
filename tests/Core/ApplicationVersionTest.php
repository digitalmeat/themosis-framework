<?php

namespace Themosis\Tests\Core;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;

class ApplicationVersionTest extends TestCase
{
    public function testLaravelVersionFollowsInstalledIlluminateMajor()
    {
        $installed = ltrim(InstalledVersions::getVersion('illuminate/support'), 'v');

        $this->assertSame(
            strtok($installed, '.'),
            strtok(Application::VERSION, '.'),
            'Application::VERSION is reported by app()->version() and must match the installed illuminate/* major.'
        );
    }

    public function testVersionMethodReturnsLaravelVersion()
    {
        $this->assertSame(Application::VERSION, (new Application())->version());
    }

    public function testThemosisVersionIsSemver()
    {
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', Application::THEMOSIS_VERSION);
    }
}
