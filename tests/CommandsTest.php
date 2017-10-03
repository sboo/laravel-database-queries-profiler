<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

/**
 * Class CommandsTest.
 */
class CommandsTest extends AbstractUnitTestCase
{
    /**
     * Test basic artisan commands execution.
     */
    public function testCommandsExecution()
    {
        $this->prepareDatabase($this->app, true);

        $commands_names = [
            'profiler:counters',
            'profiler:settings',
            'profiler:top',
            'profiler:clear',
        ];

        foreach ($commands_names as $commands_name) {
            $this->assertArtisanCommandExists($commands_name);

            // Test execution method
            $this->artisan($commands_name);
        }
    }

    /**
     * @param string $command_signature
     */
    protected function assertArtisanCommandExists($command_signature)
    {
        $this->assertNotFalse(
            $this->artisan($command_signature, ['--help']),
            'Command does not return help message'
        );
    }
}
