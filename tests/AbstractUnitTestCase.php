<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Connection as DatabaseConnection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class AbstractUnitTestCase.
 */
abstract class AbstractUnitTestCase extends BaseTestCase
{
    const DATABASE_DEFAULT_TABLE_NAME = 'test_table';

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->prepareDatabase($app, true);

        // Register our service-provider manually
        $app->register(DatabaseQueriesProfilerServiceProvider::class);

        return $app;
    }

    /**
     * Prepare the database.
     *
     * @param Application $app
     * @param bool        $force
     *
     * @throws InvalidArgumentException
     */
    protected function prepareDatabase(Application $app, $force = false)
    {
        if ($this->getConfigRepository($app)->get('database.default') !== 'sqlite') {
            throw new InvalidArgumentException('I can work only with "sqlite" database while testing');
        }

        $database_file_path      = $this->getConfigRepository($app)->get('database.connections.sqlite.database');
        $database_directory_path = File::dirname($database_file_path);

        if (!is_dir($database_directory_path)) {
            File::makeDirectory($database_directory_path, 0775, true);
        }

        if ($force === true && file_exists($database_file_path)) {
            File::delete($database_file_path);
        }

        if (!file_exists($database_file_path)) {
            File::put($database_file_path, null);
        }

        $this->getDatabaseConnection($app)->reconnect();

        if ($force === true) {
            $result = $this->getDatabaseConnection($app)->statement(sprintf(
                'CREATE TABLE %s (name text, content text);',
                static::DATABASE_DEFAULT_TABLE_NAME
            ));

            $this->assertTrue($result, 'Database were not created successfully (invalid code returned)');

            $seed_data = [
                'Some name'  => 'Some content',
                'Some name1' => 'Some content1',
                'Some name2' => 'Some content2',
            ];

            foreach ($seed_data as $name => $content) {
                $this->getDatabaseConnection($app)->insert(
                    'insert into ' . static::DATABASE_DEFAULT_TABLE_NAME . ' (name, content) values (?, ?)', [
                    $name, $content,
                ]);
            }
        }
    }

    /**
     * @return QueryBuilder
     */
    protected function getTestTableQueryBuilderInstance()
    {
        return DB::table(static::DATABASE_DEFAULT_TABLE_NAME);
    }

    /**
     * @param Application $app
     *
     * @return ConfigRepository
     */
    protected function getConfigRepository(Application $app)
    {
        return $app->make('config');
    }

    /**
     * Assert value is float.
     *
     * @param $value
     */
    protected function assertIsFloat($value)
    {
        $this->assertTrue(is_float($value), 'Value has not float type');
    }

    /**
     * @param Application $app
     *
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection(Application $app)
    {
        return $app->make('db');
    }

    /**
     * @param        $object
     * @param string $method_name
     */
    protected function assertMethodExists($object, $method_name)
    {
        $this->assertTrue(method_exists($object, $method_name), 'Object does not have method "' . $method_name . '"');
    }
}
