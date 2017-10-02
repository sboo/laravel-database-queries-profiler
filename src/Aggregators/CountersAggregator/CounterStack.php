<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;

/**
 * Class CounterStack
 *
 * Counter stack storage container.
 */
class CounterStack implements Arrayable
{
    /**
     * @var DatabaseQuery[]|array
     */
    protected $stack = [];

    /**
     * CounterStack constructor.
     *
     * @param DatabaseQuery[]|array $queries_array
     */
    public function __construct($queries_array = [])
    {
        foreach ($queries_array as &$query) {
            if ($query instanceof DatabaseQuery) {
                array_push($this->stack, $query);
            }
        }
    }

    /**
     * Push an DatabaseQuery object into stack.
     *
     * @param DatabaseQuery $query
     *
     * @return void
     */
    public function push(DatabaseQuery $query)
    {
        array_push($this->stack, $query);
    }

    /**
     * Get maximum duration value of stack items.
     *
     * @return float
     */
    public function getMaximumDuration()
    {
        return ($stack = $this->getStackDurationsValues()) && count($stack) ? max($stack) : 0.0;
    }

    /**
     * Get stack durations values as an array.
     *
     * @return float[]|array
     */
    protected function getStackDurationsValues()
    {
        $result = [];

        foreach ($this->stack as $query) {
            $duration = $query->getDuration();

            if (!is_null($duration)) {
                array_push($result, (float) $query->getDuration());
            }
        }

        return $result;
    }

    /**
     * Get minimum duration value of stack items.
     *
     * @return float
     */
    public function getMinimumDuration()
    {
        return ($stack = $this->getStackDurationsValues()) && count($stack) ? min($stack) : 0.0;
    }

    /**
     * Get averaged duration value of stack items.
     *
     * @return float
     */
    public function getAveragedDuration()
    {
        return $this->getRoundedFloatValue($this->getStackDurationsValues());
    }

    /**
     * Returns rounded values for array of float values.
     *
     * @param float[] ...$values
     *
     * @return float
     */
    protected function getRoundedFloatValue($values = [0.0])
    {
        $floated_values_stack = [];

        foreach ($values as &$value) {
            if (($value = floatval($value)) && $value > 0) {
                array_push($floated_values_stack, $value);
            }
        }

        if (!empty($floated_values_stack)) {
            try {
                return array_sum($floated_values_stack) / count($floated_values_stack);
            } catch (Exception $e) {
                // Do nothing
            }
        }

        return 0.0;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return (array) $this->stack;
    }

    /**
     * Remove all stack items with 'when' date older then passed to the method.
     *
     * @param Carbon $older_then
     *
     * @return void
     */
    public function removeAllQueriesOlderThen(Carbon $older_then)
    {
        $changes_made = false;

        foreach ($this->stack as $key => &$query) {
            $should_be_removed = false;

            if ($query instanceof DatabaseQuery) {
                $when = $query->getWhen();

                if ($when instanceof Carbon) {
                    if ($older_then->gt($query->getWhen())) {
                        $should_be_removed = true;
                    }
                } else {
                    $should_be_removed = true;
                }
            } else {
                $should_be_removed = true;
            }

            if ($should_be_removed) {
                unset($this->stack[$key]);

                $changes_made = true;
            }
        }

        if ($changes_made) {
            $this->stack = array_filter($this->stack);
            $this->stack = array_values($this->stack);
        }
    }
}
