<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator;

use Exception;
use Carbon\Carbon;
use InvalidArgumentException;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\AbstractAggregator;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerInterface;

/**
 * Class CountersAggregator.
 */
class CountersAggregator extends AbstractAggregator
{
    /**
     * @var CounterStack
     */
    protected $last_five_seconds;

    /**
     * @var CounterStack
     */
    protected $last_fifteen_seconds;

    /**
     * @var CounterStack
     */
    protected $last_minute;

    /**
     * {@inheritdoc}
     */
    public function __construct(DatabaseQueriesProfilerInterface $profiler)
    {
        parent::__construct($profiler);

        // Initialize counters objects
        foreach (array_keys($this->toArray()) as $stack_name) {
            $this->{$stack_name} = new CounterStack;
        }
    }

    /**
     * Get 'last five seconds' counter object.
     *
     * @return CounterStack
     */
    public function lastFiveSeconds()
    {
        return $this->last_five_seconds;
    }

    /**
     * Get 'last fifteen seconds' counter object.
     *
     * @return CounterStack
     */
    public function lastFifteenSeconds()
    {
        return $this->last_fifteen_seconds;
    }

    /**
     * Get 'last minute' counter object.
     *
     * @return CounterStack
     */
    public function lastMinute()
    {
        return $this->last_minute;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $from_storage = (array) $this->getStorageRepository()->get($this->getStorageKeyName());

        foreach (array_keys($this->toArray()) as $stack_name) {
            if (isset($from_storage[$stack_name]) && $from_storage[$stack_name] instanceof CounterStack) {
                $this->{$stack_name} = $from_storage[$stack_name];
            } else {
                $this->{$stack_name} = new CounterStack;
            }
        }

        $this->clean();
    }

    /**
     * {@inheritdoc}
     *
     * Warning! Array key name must equals stack property name!
     */
    public function toArray()
    {
        return [
            'last_five_seconds'    => $this->last_five_seconds,
            'last_fifteen_seconds' => $this->last_fifteen_seconds,
            'last_minute'          => $this->last_minute,
        ];
    }

    /**
     * Get counter stack by name.
     *
     * @param string $counter_name
     *
     * @throws InvalidArgumentException
     *
     * @return CounterStack
     */
    public function getCounterByName($counter_name)
    {
        try {
            return $this->{$counter_name};
        } catch (Exception $e) {
            throw new InvalidArgumentException('Invalid counter name', 404, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->clean();

        $this->getStorageRepository()->forever($this->getStorageKeyName(), (array) $this->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        $this->last_five_seconds->removeAllQueriesOlderThen(Carbon::now()->subSeconds(5));
        $this->last_fifteen_seconds->removeAllQueriesOlderThen(Carbon::now()->subSeconds(15));
        $this->last_minute->removeAllQueriesOlderThen(Carbon::now()->subSeconds(60));
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return (bool) $this->getConfigValue('counters.enabled', true);
    }

    /**
     * {@inheritdoc}
     */
    public function clear($in_storage_too = true)
    {
        foreach (array_keys($this->toArray()) as $stacks_name) {
            $this->{$stacks_name} = new CounterStack;
        }

        if ($in_storage_too === true) {
            $this->getStorageRepository()->forget($this->getStorageKeyName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function aggregate(DatabaseQuery $query, $and_save = true)
    {
        $this->last_five_seconds->push($query);
        $this->last_fifteen_seconds->push($query);
        $this->last_minute->push($query);

        if ($and_save === true) {
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getStorageKeyName()
    {
        return 'database_queries_profiler_counters';
    }
}
