<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Cache\Repository as CacheRepository;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerInterface;

/**
 * Class AbstractAggregator.
 *
 * Abstract aggregator class.
 */
abstract class AbstractAggregator implements AggregatorInterface, Arrayable
{
    /**
     * Profiler instance.
     *
     * @var DatabaseQueriesProfilerInterface
     */
    protected $profiler;

    /**
     * AbstractAggregator constructor.
     *
     * @param DatabaseQueriesProfilerInterface $profiler
     */
    public function __construct(DatabaseQueriesProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function load();

    /**
     * {@inheritdoc}
     */
    abstract public function save();

    /**
     * {@inheritdoc}
     */
    abstract public function clear($in_storage_too = true);

    /**
     * {@inheritdoc}
     */
    abstract public function clean();

    /**
     * {@inheritdoc}
     */
    abstract public function isEnabled();

    /**
     * {@inheritdoc}
     */
    abstract public function aggregate(DatabaseQuery $query, $and_save = true);

    /**
     * Get data storage repository instance.
     *
     * @return CacheRepository|null
     */
    protected function getStorageRepository()
    {
        return $this->profiler->getStorageRepository();
    }

    /**
     * Get config value (you do not need to pass config root prefix).
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getConfigValue($key, $default = null)
    {
        return $this->profiler->getConfigValue($key, $default);
    }

    /**
     * Returns storage key name.
     *
     * @return string
     */
    abstract protected function getStorageKeyName();
}
