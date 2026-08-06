<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;
use Themosis\Core\AliasLoader;

class AliasLoaderTest extends TestCase
{
    /**
     * AliasLoader is a static singleton with no reset hook, so the aliases
     * these tests register (including the deliberately-bogus 'foo' => 'doe'
     * below) stay registered on the process-wide spl_autoload stack for the
     * rest of the PHPUnit run. That was harmless under Illuminate <12: no
     * other test resolved a plain 'foo' abstract through class_exists().
     * Illuminate\Container\Container::isShared() (illuminate/container
     * ^12.0) now calls class_exists($abstract) unconditionally, so
     * ApplicationTest's deferred-service tests -- which legitimately use
     * 'foo' as an abstract name -- trigger this loader's leftover
     * 'foo' => 'doe' alias and fatal on class_alias('doe', 'foo') because
     * "doe" isn't a real class. Clearing the aliases once this class is
     * done (not per-test: testAliasAreRegisteredAfterInstantiation asserts
     * on state accumulated from the two tests before it) keeps the
     * singleton from leaking into later test classes.
     */
    public static function tearDownAfterClass(): void
    {
        AliasLoader::getInstance()->setAliases([]);
    }

    public function testLoaderCanBeCreatedAndRegisteredOnce()
    {
        $loader = AliasLoader::getInstance(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $loader->getAliases());
        $this->assertFalse($loader->isRegistered());

        $loader->register();
        $this->assertTrue($loader->isRegistered());
    }

    public function testGetInstanceCreateOneInstance()
    {
        $loader = AliasLoader::getInstance(['some' => 'thing']);
        $this->assertSame($loader, AliasLoader::getInstance());
    }

    public function testAliasAreRegisteredAfterInstantiation()
    {
        $loader = AliasLoader::getInstance(['bar' => 'baz']);
        $loader->alias('foo', 'doe');

        // Still same instance on all tests.
        $this->assertEquals([
            'bar' => 'baz',
            'foo' => 'doe',
            'some' => 'thing',
        ], $loader->getAliases());
    }
}
