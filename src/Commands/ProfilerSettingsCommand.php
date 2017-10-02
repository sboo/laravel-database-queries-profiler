<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Commands;

use Illuminate\Support\Str;
use Tarampampam\LaravelDatabaseQueriesProfiler\Aggregators\CountersAggregator\CounterStack;

/**
 * Class ProfilerSettingsCommand
 *
 * @todo: Write class description.
 */
class ProfilerSettingsCommand extends AbstractCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'profiler:settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show database queries profiler settings';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $settings = array_dot(config($this->profiler::getConfigRootKeyName()));

        array_walk($settings, function (&$value, $key) {
            $value = [$key, is_bool($value) ? ($value ? 'true' : 'false') : (is_scalar($value) ? $value : '-')];
        });

        $this->table(['Name', 'Value'], $settings);
    }
}
