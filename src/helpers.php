<?php

use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerFacade;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CountersAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\TopQueriesAggregator\TopQueriesAggregator;

if (! function_exists('dbProfilerRegisterQuery')) {
    /**
     * Register database query.
     *
     * @param DatabaseQuery|array|Traversable $query
     *
     * @return void
     */
    function dbProfilerRegisterQuery($query)
    {
        DatabaseQueriesProfilerFacade::registerQuery($query);
    }
}

if (! function_exists('dbProfilerCounters')) {
    /**
     * Get the 'counters' aggregator.
     *
     * @return CountersAggregator
     */
    function dbProfilerCounters()
    {
        return DatabaseQueriesProfilerFacade::counters();
    }
}

if (! function_exists('dbProfilerTop')) {
    /**
     * Get the 'top' aggregator.
     *
     * @return TopQueriesAggregator
     */
    function dbProfilerTop()
    {
        return DatabaseQueriesProfilerFacade::top();
    }
}
