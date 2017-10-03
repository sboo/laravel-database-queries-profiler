<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

use Illuminate\Support\Collection;
use Illuminate\Log\Writer as IlluminateLogWriter;
use Illuminate\Cache\Repository as CacheRepository;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfiler;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CounterStack;
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

        $this->prepareDatabase($this->app, true);
        $this->instance = new DatabaseQueriesProfiler($this->app);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->instance->clearAll();
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
     * Test '->registerQuery()' method.
     *
     * @return void
     */
    public function testRegisterQueryWithFakedQueryObjects()
    {
        $test_array = ['sql' => 'select * from "fucking_asshole"', 'duration' => 3.14];

        $query = new DatabaseQuery($test_array);

        foreach ([$query, $test_array] as $item) {
            $this->instance->registerQuery($item);
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

    /**
     * Test '->registerQuery()' method with .
     *
     * @return void
     */
    public function testRegisterQueryWithRealDatabaseRequests()
    {
        $unique_name    = 'unique_name_' . rand(1, 999999);
        $unique_content = 'Unique content #' . rand(1, 999999);

        // Insert data for test
        $this->assertTrue($this->getTestTableQueryBuilderInstance()->insert([
            'name'    => $unique_name,
            'content' => $unique_content,
        ]));

        // Execute query
        $result = $this->getTestTableQueryBuilderInstance()->get();

        // Assert query result object instance
        $this->assertInstanceOf(Collection::class, $result);

        $query_profiled = false;
        foreach ($this->instance->top()->toArray() as &$query) {
            $this->assertInstanceOf(DatabaseQuery::class, $query);

            /** @var DatabaseQuery $query */
            if (str_contains($query->getQueryContent(), [$unique_name, $unique_content])) {
                $query_profiled = true;
            }
        }
        $this->assertTrue($query_profiled, 'Query was NOT profiled!');

        $counters_as_array = $this->instance->counters()->toArray();
        foreach (['last_five_seconds', 'last_fifteen_seconds', 'last_minute'] as $counter_name) {
            /** @var CounterStack $counter */
            $counter = $counters_as_array[$counter_name];

            $this->assertInstanceOf(CounterStack::class, $counter);

            foreach ([
                         $counter->getMaximumDuration(),
                         $counter->getMinimumDuration(),
                         $counter->getAveragedDuration(),
                     ] as $value) {
                $this->assertIsFloat($value);
                $this->assertTrue($value > 0);
            }

            $this->assertInstanceOf(CounterStack::class, $this->instance->counters($counter_name));
        }
    }
}
