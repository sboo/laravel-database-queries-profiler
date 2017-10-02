<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Commands;

use Illuminate\Support\Str;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CounterStack;

/**
 * Class ProfilerCountersCommand.
 *
 * @todo: Write class description.
 */
class ProfilerCountersCommand extends AbstractCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'profiler:counters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show database queries durations <comment>counters</comment>';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $table_rows = [];

        foreach ($this->profiler->counters()->toArray() as $counter_name => $counter_stack) {
            if ($counter_stack instanceof CounterStack) {
                array_push($table_rows, [
                    Str::ucfirst(str_replace(['_', '-'], ' ', strval($counter_name))),
                    sprintf(
                        'Averaged: %.2F (min: %.2F, max: %.2F)',
                        $counter_stack->getAveragedDuration(),
                        $counter_stack->getMinimumDuration(),
                        $counter_stack->getMaximumDuration()
                    ),
                ]);
            }
        }

        $this->table(['Counter name', 'Duration time'], $table_rows);
    }
}
