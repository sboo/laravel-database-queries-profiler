<?php

use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerFacade;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CountersAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\TopQueriesAggregator\TopQueriesAggregator;

if (! function_exists('dbProfilerRegisterQuery')) {
    /**
     * Handy register database query.
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
     * @param string|null $counter_name
     *
     * @return CountersAggregator
     */
    function dbProfilerCounters($counter_name = null)
    {
        return DatabaseQueriesProfilerFacade::counters($counter_name);
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
