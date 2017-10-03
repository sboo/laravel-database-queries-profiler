<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler;

use Traversable;
use Illuminate\Contracts\Foundation\Application;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CountersAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\TopQueriesAggregator\TopQueriesAggregator;

/**
 * Class DatabaseQueriesProfiler.
 *
 * Database queries profiler.
 */
class DatabaseQueriesProfiler extends AbstractDatabaseQueriesProfiler
{
    /**
     * @var TopQueriesAggregator
     */
    protected $top;

    /**
     * @var CountersAggregator
     */
    protected $counters;

    /**
     * DatabaseQueriesProfiler constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->top      = new TopQueriesAggregator($this);
        $this->counters = new CountersAggregator($this);

        if ($this->top->isEnabled()) {
            $this->top->load();
        }

        if ($this->counters->isEnabled()) {
            $this->counters->load();
        }
    }

    /**
     * Register database query.
     *
     * @param DatabaseQuery|array|Traversable $query
     */
    public function requesterQuery($query)
    {
        $query = $query instanceof DatabaseQuery
            ? $query
            : ($query instanceof Traversable || is_array($query)
                ? new DatabaseQuery($query)
                : null
            );

        if ($query instanceof DatabaseQuery && $query->isValid()) {
            $log_enabled = (bool) $this->getConfigValue('logging.queries.all.enabled', false);

            if ($log_enabled) {
                $this->writeQueryIntoLog($query);
            }

            if ($this->top->isEnabled()) {
                $this->top->load(); // For multi-threads supports

                if (! $this->top->queryContentIsInExcludesList($query)) {
                    if (
                        ! $this->top->stackIsFull()
                        || $this->top->queryDurationIsMoreThenExistsInTopQueriesStack($query)
                    ) {
                        $this->top->aggregate($query, true);
                    }
                }
            }

            if ($this->counters->isEnabled()) {
                $this->counters->load(); // For multi-threads supports

                $this->counters->aggregate($query, true);
            }
        }
    }

    /**
     * Get the 'counters' aggregator.
     *
     * @return CountersAggregator
     */
    public function counters()
    {
        if ($this->counters->isEnabled()) {
            $this->counters->load();
        }

        return $this->counters;
    }

    /**
     * Get the 'top' aggregator.
     *
     * @return TopQueriesAggregator
     */
    public function top()
    {
        if ($this->top->isEnabled()) {
            $this->top->load();
        }

        return $this->top;
    }

    /**
     * Clear all aggregated data.
     *
     * @return void
     */
    public function clearAll()
    {
        $this->top->clear(true);
        $this->counters->clear(true);
    }

    /**
     * Write query object into log file.
     *
     * @param DatabaseQuery $query
     */
    protected function writeQueryIntoLog(DatabaseQuery $query)
    {
        $log_level = (string) $this->getConfigValue('logging.queries.all.level', 'debug');

        $this->getLoggerInstance()->write(
            $log_level,
            'Query to the database was caught',
            $query->toArray()
        );
    }
}
