<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler;

use Illuminate\Support\Facades\Facade;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CountersAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\TopQueriesAggregator\TopQueriesAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;

/**
 * Class DatabaseQueriesProfilerFacade
 *
 * Database queries profiler facade.
 *
 * @method static DatabaseQueriesProfiler instance()
 * @method static void requesterQuery(DatabaseQuery|array $query)
 * @method static CountersAggregator counters()
 * @method static TopQueriesAggregator top()
 */
class DatabaseQueriesProfilerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'DatabaseQueriesProfiler';
    }
}
