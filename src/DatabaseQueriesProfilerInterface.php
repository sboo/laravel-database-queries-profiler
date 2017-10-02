<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler;

use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Writer as IlluminateLogWriter;

/**
 * Interface DatabaseQueriesProfilerInterface
 */
interface DatabaseQueriesProfilerInterface
{
    /**
     * AbstractDatabaseQueriesProfiler constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app);

    /**
     * Return self (for facade as example).
     *
     * @return $this|static
     */
    public function instance();

    /**
     * Get logger container instance.
     *
     * @return IlluminateLogWriter|mixed
     */
    public function getLoggerInstance();

    /**
     * Get data storage repository instance.
     *
     * @return CacheRepository|null
     */
    public function getStorageRepository();

    /**
     * Get config value (you do not need to pass config root prefix).
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConfigValue($key, $default = null);

    /**
     * Get config root key name.
     *
     * @return string
     */
    public static function getConfigRootKeyName();
}
