<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler;

use Traversable;
use Illuminate\Support\Facades\Facade;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CountersAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\TopQueriesAggregator\TopQueriesAggregator;

/**
 * Class DatabaseQueriesProfilerFacade.
 *
 * Database queries profiler facade.
 *
 * @method static DatabaseQueriesProfiler instance()
 * @method static void requesterQuery(DatabaseQuery|array|Traversable $query)
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
