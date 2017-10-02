<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

use Illuminate\Log\Writer as IlluminateLogWriter;
use Illuminate\Cache\Repository as CacheRepository;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfiler;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CountersAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\TopQueriesAggregator\TopQueriesAggregator;

/**
 * Class DatabaseQueriesProfilerTest.
 */
class DatabaseQueriesProfilerTest extends AbstractUnitTestCase
{
    /**
     * @var DatabaseQueriesProfiler
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->instance = new DatabaseQueriesProfiler($this->app);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->instance);

        parent::tearDown();
    }

    /**
     * Test '->instance()' method.
     *
     * @return void
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DatabaseQueriesProfiler::class, $this->instance->instance());
    }

    /**
     * Test '->getLoggerInstance()' method.
     *
     * @return void
     */
    public function testGetLoggerInstance()
    {
        $this->assertInstanceOf(IlluminateLogWriter::class, $this->instance->getLoggerInstance());
    }

    /**
     * Test '->getStorageRepository()' method.
     *
     * @return void
     */
    public function testGetStorageRepository()
    {
        $this->assertInstanceOf(CacheRepository::class, $this->instance->getStorageRepository());
    }

    /**
     * Test '->getConfigValue()' method.
     *
     * @return void
     */
    public function testGetConfigValue()
    {
        $this->assertTrue(is_bool($this->instance->getConfigValue('enabled')));
        $this->assertTrue(is_array($this->instance->getConfigValue('storage')));
    }

    /**
     * Test '->getConfigRootKeyName()' method.
     *
     * @return void
     */
    public function testGetConfigRootKeyName()
    {
        $this->assertTrue(is_array(config($this->instance->getConfigRootKeyName())));
    }

    /**
     * Test '->requesterQuery()' method.
     *
     * @return void
     */
    public function testRequesterQuery()
    {
        $test_array = ['sql' => 'select * from "fucking_asshole"', 'duration' => 3.14];

        $query = new DatabaseQuery($test_array);

        foreach ([$query, $test_array] as $item) {
            $this->instance->requesterQuery($item);
        }

        $this->assertNotEmpty($this->instance->top()->toArray());
    }

    /**
     * Test aggregators accessors methods.
     *
     * @return void
     */
    public function testAggregators()
    {
        $this->assertInstanceOf(TopQueriesAggregator::class, $this->instance->top());
        $this->assertInstanceOf(CountersAggregator::class, $this->instance->counters());
    }
}
