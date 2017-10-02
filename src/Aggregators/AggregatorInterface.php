<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators;

use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;

/**
 * Interface AggregatorInterface
 *
 * Aggregator interface.
 */
interface AggregatorInterface
{
    /**
     * Load aggregator data from permanent storage into runtime.
     *
     * @return void
     */
    public function load();

    /**
     * Save aggregator runtime data into permanent storage.
     *
     * @return void
     */
    public function save();

    /**
     * Clear (remove all) aggregator data. Optionally you can clear *only* runtime storage.
     *
     * @param bool $in_storage_too
     *
     * @return void
     */
    public function clear($in_storage_too = true);

    /**
     * Make runtime storage cleaning (remove outdated, etc).
     *
     * @return void
     */
    public function clean();

    /**
     * Return true, if current aggregator is enabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Put some database query object into aggregator.
     *
     * @param DatabaseQuery $query
     * @param bool          $and_save
     *
     * @return void
     */
    public function aggregate(DatabaseQuery $query, $and_save = true);
}
