<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Contracts\Foundation\Application;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;
use Tarampampam\LaravelDatabaseQueriesProfiler\Commands\ProfilerTopCommand;
use Tarampampam\LaravelDatabaseQueriesProfiler\Commands\ProfilerClearCommand;
use Tarampampam\LaravelDatabaseQueriesProfiler\Commands\ProfilerCountersCommand;
use Tarampampam\LaravelDatabaseQueriesProfiler\Commands\ProfilerSettingsCommand;

/**
 * Class DatabaseQueriesProfilerServiceProvider.
 *
 * Service provider for database queries profiler.
 */
class DatabaseQueriesProfilerServiceProvider extends ServiceProvider
{
    /**
     * Path to the package config.
     */
    const CONFIG_PATH = __DIR__ . '/../config/database-queries-profiler.php';

    /**
     * Array of "bad" database storage drivers names.
     *
     * @var array
     */
    protected $bad_storage_drivers = ['file', 'database', 'array'];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->initializeConfigs();
        $this->registerDatabaseQueriesProfiler();

        if ($this->getConfigValue('enabled')) {
            $this->checkStorageDriver();
            $this->attachEventsListener();
            $this->registerCommands();
        }
    }

    /**
     * Retrieve commands classes.
     *
     * @return void
     */
    public function registerCommands()
    {
        if ($this->getConfigValue('top.enabled')) {
            $this->commands(ProfilerTopCommand::class);
        }

        if ($this->getConfigValue('counters.enabled')) {
            $this->commands(ProfilerCountersCommand::class);
        }

        $this->commands(ProfilerSettingsCommand::class);
        $this->commands(ProfilerClearCommand::class);
    }

    /**
     * Get config root key name.
     *
     * @return string
     */
    public static function getConfigRootKeyName()
    {
        return 'database-queries-profiler';
    }

    /**
     * Generate user notice when used "bad" storage driver.
     *
     * @return void
     */
    protected function checkStorageDriver()
    {
        $used_storage = $this->getConfigValue('storage.use');

        if ($this->isProduction() && in_array($used_storage, $this->bad_storage_drivers)) {
            trigger_error(sprintf(
                'Use storage driver "%s" is bad idea for database queries profiler', $used_storage
            ), E_USER_NOTICE);
        }
    }

    /**
     * Returns true, if app environment is production.
     *
     * @return bool
     */
    protected function isProduction()
    {
        return Str::contains($this->app->environment(), 'prod');
    }

    /**
     * Initialize configs.
     *
     * @return void
     */
    protected function initializeConfigs()
    {
        $this->mergeConfigFrom(static::CONFIG_PATH, static::getConfigRootKeyName());

        $this->publishes([
            realpath(static::CONFIG_PATH) => config_path(basename(static::CONFIG_PATH)),
        ], 'config');
    }

    /**
     * Register database queries profiler container instance.
     *
     * @return void
     */
    protected function registerDatabaseQueriesProfiler()
    {
        $this->app->singleton(DatabaseQueriesProfiler::class, function (Application $app) {
            return new DatabaseQueriesProfiler($app);
        });

        $this->app->bind('DatabaseQueriesProfiler', DatabaseQueriesProfiler::class);
    }

    /**
     * Get config value (you do not need to pass config root prefix).
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getConfigValue($key, $default = null)
    {
        /** @var \Illuminate\Contracts\Config\Repository $instance */
        static $instance = null;

        if (is_null($instance)) {
            $instance = $this->app->make('config');
        }

        return $instance->get(sprintf('%s.%s', static::getConfigRootKeyName(), $key), $default);
    }

    /**
     * Attach events listener.
     *
     * @return void
     */
    protected function attachEventsListener()
    {
        Event::listen(QueryExecuted::class, function (QueryExecuted $query) {
            try {
                $bindings        = array_map('strval', $query->bindings);
                $duration        = floatval($query->time);
                $connection_name = (string) $query->connection->getName();

                // Insert bindings into query
                $query = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
                $query = vsprintf($query, $bindings);

                // Register query
                $this->app->make('DatabaseQueriesProfiler')->registerQuery(new DatabaseQuery([
                    'bindings'        => $bindings,
                    'duration'        => $duration,
                    'connection_name' => $connection_name,
                    'query'           => $query,
                ]));
            } catch (Exception $e) {
                Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            }
        });
    }
}
