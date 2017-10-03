<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CountersAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\TopQueriesAggregator\TopQueriesAggregator;

/**
 * Class HelpersTest.
 */
class HelpersTest extends AbstractUnitTestCase
{
    /**
     * Test helpers functions.
     */
    public function testHelpers()
    {
        $this->assertTrue(function_exists('dbProfilerRegisterQuery'));
        $this->assertNull(dbProfilerRegisterQuery([]));

        $this->assertInstanceOf(CountersAggregator::class, dbProfilerCounters());
        $this->assertInstanceOf(TopQueriesAggregator::class, dbProfilerTop());
    }
}
