<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\TopQueriesAggregator;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\AbstractAggregator;

/**
 * Class TopQueriesAggregator.
 */
class TopQueriesAggregator extends AbstractAggregator
{
    /**
     * Top queries stack.
     *
     * @var DatabaseQuery[]|array
     */
    protected $stack = [];

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $this->stack = (array) $this->getStorageRepository()->get($this->getStorageKeyName());

        $this->clean();
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->clean();

        $this->getStorageRepository()->forever($this->getStorageKeyName(), (array) $this->stack);
    }

    /**
     * {@inheritdoc}
     */
    public function clear($in_storage_too = true)
    {
        $this->stack = [];

        if ($in_storage_too === true) {
            $this->getStorageRepository()->forget($this->getStorageKeyName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        if (! empty($this->stack)) {
            $changes_made  = false;
            $lifetime      = (int) $this->getConfigValue('top.lifetime', 60 * 60 * 5);
            $now_timestamp = Carbon::now()->getTimestamp();

            foreach ($this->stack as $key => $stacked_query) {
                $should_be_removed = false;

                if (($when = $stacked_query->getWhen()) && $when instanceof Carbon) {
                    if ($now_timestamp - $when->getTimestamp() > $lifetime) {
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

    /**
     * Returns true, if passed database query object has 'duration' value more than any exists object in stack.
     *
     * @param DatabaseQuery $query
     *
     * @return bool
     */
    public function queryDurationIsMoreThenExistsInTopQueriesStack(DatabaseQuery $query)
    {
        if (! empty($this->stack)) {
            foreach ($this->stack as $stacked_query) {
                if ($query->getDuration() > $stacked_query->getDuration()) {
                    return true;
                }
            }
        } else {
            return true;
        }

        return false;
    }

    /**
     * Returns true, if passed database query object has query content wits words, declared in excludes list.
     *
     * @param DatabaseQuery $query
     *
     * @return bool
     */
    public function queryContentIsInExcludesList(DatabaseQuery $query)
    {
        static $excludes_list = null;

        if ((bool) $this->getConfigValue('top.exclude.enabled', true)) {
            if (is_null($excludes_list)) {
                $excludes_list = array_filter((array) $this->getConfigValue('top.exclude.list', []));
            }

            if (! empty($excludes_list)) {
                $query_content = Str::lower((string) $query->getQueryContent());

                foreach ($excludes_list as $exclude) {
                    if (Str::contains($query_content, Str::lower($exclude))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Make top queries stack sorting (by duration value).
     *
     * @return void
     */
    public function sortStackByDuration()
    {
        usort($this->stack, function (DatabaseQuery &$a, DatabaseQuery &$b) {
            if ($a->getDuration() === $b->getDuration()) {
                return 0;
            }

            return ($a->getDuration() < $b->getDuration()) ? 1 : -1;
        });
    }

    /**
     * Get top queries stack size value.
     *
     * @return int
     */
    public function getStackSize()
    {
        return count($this->stack);
    }

    /**
     * Get top queries stack maximum size.
     *
     * @return int
     */
    public function getStackMaximumSize()
    {
        return (int) $this->getConfigValue('top.size', 100);
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return (bool) $this->getConfigValue('top.enabled', true);
    }

    /**
     * Return true if stack is full.
     *
     * @return bool
     */
    public function stackIsFull()
    {
        return $this->getStackSize() > $this->getStackMaximumSize();
    }

    /**
     * {@inheritdoc}
     */
    public function aggregate(DatabaseQuery $query, $and_save = true)
    {
        $this->load();

        array_push($this->stack, $query);

        $this->sortStackByDuration();

        // Remove last elements, if needed
        if ($this->getStackSize() > ($max = $this->getStackMaximumSize())) {
            $this->stack = array_slice($this->stack, 0, $max);
        }

        if ($and_save === true) {
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return (array) $this->stack;
    }

    /**
     * {@inheritdoc}
     */
    protected function getStorageKeyName()
    {
        return 'database_queries_profiler_top';
    }
}
