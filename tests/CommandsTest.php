<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfiler;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerFacade;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerServiceProvider;

/**
 * Class CommandsTest.
 */
class CommandsTest extends AbstractUnitTestCase
{
    /**
     * @param string $command_signature
     */
    protected function assertArtisanCommandExists(string $command_signature): void
    {
        $this->assertNotFalse(
            $this->artisan($command_signature, ['--help']),
            'Command does not return help message'
        );
    }

    /**
     * Test basic artisan commands execution.
     */
    public function testCommandsExecution()
    {
        $commands_names = [
            'profiler:counters',
            'profiler:settings',
            'profiler:top',
        ];

        foreach ($commands_names as $commands_name) {
            $this->assertArtisanCommandExists($commands_name);
        }
    }
}
