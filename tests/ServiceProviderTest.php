<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfiler;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerFacade;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerServiceProvider;

/**
 * Class ServiceProviderTest.
 */
class ServiceProviderTest extends AbstractUnitTestCase
{
    /**
     * Tests service-provider loading.
     */
    public function testServiceProviderLoading()
    {
        $this->assertInstanceOf(DatabaseQueriesProfiler::class, $this->app['DatabaseQueriesProfiler']);
        $this->assertInstanceOf(DatabaseQueriesProfiler::class, $this->app[DatabaseQueriesProfiler::class]);
        $this->assertInstanceOf(DatabaseQueriesProfiler::class, app('DatabaseQueriesProfiler'));
        $this->assertInstanceOf(DatabaseQueriesProfiler::class, app(DatabaseQueriesProfiler::class));
    }

    /**
     * Test accessible from facade.
     */
    public function testAccessibleFromFacade()
    {
        $this->assertInstanceOf(DatabaseQueriesProfiler::class, DatabaseQueriesProfilerFacade::instance());
    }

    /**
     * Test default configs values.
     */
    public function testDefaultConfigValues()
    {
        $config = config(DatabaseQueriesProfilerServiceProvider::getConfigRootKeyName());

        $this->assertTrue(is_bool($config['enabled']));
        $this->assertTrue(is_string($config['storage']['use']));

        $this->assertTrue(is_bool($config['top']['enabled']));
        $this->assertTrue(is_int($config['top']['size']));
        $this->assertTrue(is_int($config['top']['lifetime']));
        $this->assertTrue(is_bool($config['top']['exclude']['enabled']));
        $this->assertTrue(is_array($config['top']['exclude']['list']));

        $this->assertTrue(is_bool($config['counters']['enabled']));

        $this->assertTrue(is_bool($config['logging']['queries']['all']['enabled']));
        $this->assertTrue(is_string($config['logging']['queries']['all']['level']));
    }
}
