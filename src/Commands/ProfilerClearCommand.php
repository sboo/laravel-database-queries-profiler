<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Commands;

/**
 * Class ProfilerClearCommand.
 *
 * @todo: Write class description.
 */
class ProfilerClearCommand extends AbstractCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'profiler:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all aggregated profiler data';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->profiler->clearAll();

        $this->info('All aggregated data were removed');
    }
}
