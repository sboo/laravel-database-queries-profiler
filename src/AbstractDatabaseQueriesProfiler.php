<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler;

use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Writer as IlluminateLogWriter;

/**
 * Class AbstractDatabaseQueriesProfiler
 *
 * Abstract database queries profiler.
 */
abstract class AbstractDatabaseQueriesProfiler implements DatabaseQueriesProfilerInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function instance()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoggerInstance()
    {
        static $instance = null;

        if (!($instance instanceof IlluminateLogWriter)) {
            $instance = $this->app->make('log');
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageRepository()
    {
        static $instance = null;

        if (!($instance instanceof CacheRepository)) {
            $storage = (string) $this->getConfigValue('storage.use', 'file');

            $instance = $this->app->make('cache')->store($storage);
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigValue($key, $default = null)
    {
        static $instance = null;

        if (!($instance instanceof ConfigRepository)) {
            $instance = $this->app->make('config');
        }

        return $instance->get(sprintf('%s.%s', static::getConfigRootKeyName(), $key), $default);
    }

    /**
     * {@inheritdoc}
     */
    public static function getConfigRootKeyName()
    {
        return DatabaseQueriesProfilerServiceProvider::getConfigRootKeyName();
    }
}
