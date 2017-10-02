<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerServiceProvider;

/**
 * Class AbstractUnitTestCase.
 */
abstract class AbstractUnitTestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Регистрируем ручками наш (тестируемый) сервис-провайдер
        $app->register(DatabaseQueriesProfilerServiceProvider::class);

        return $app;
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
