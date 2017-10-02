<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfiler;

/**
 * Class AbstractCommand
 *
 * Abstract command class.
 */
abstract class AbstractCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'profiler:some_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Some command description';

    /**
     * Profiler instance.
     *
     * @var DatabaseQueriesProfiler
     */
    protected $profiler;

    /**
     * {@inheritdoc}
     */
    public function __construct(DatabaseQueriesProfiler $profiler)
    {
        parent::__construct();

        $this->profiler = $profiler;
    }

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws Exception
     */
    abstract public function handle();
}
