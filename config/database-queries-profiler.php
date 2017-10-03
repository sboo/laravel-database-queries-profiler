<?php

use Illuminate\Support\Str;

return [

    /*
     |--------------------------------------------------------------------------
     | Laravel database profiler settings
     |--------------------------------------------------------------------------
     */

    // Profiler is enabled?
    'enabled' => (bool) env('DATABASE_QUERIES_PROFILER_ENABLED', true),

    // Statistics (and any other data) storage (we use one of 'cache' storage).
    'storage' => [

        // Storage driver name. Set one of "cache.stores" storage name.
        // Default: "file", recommended: "redis".
        'use' => env('CACHE_DRIVER', 'file'),

    ],

    // Top (most "expensive") queries statistics
    'top' => [

        // ..is enabled?
        'enabled' => (bool) env('DATABASE_QUERIES_PROFILER_TOP_ENABLED', true),

        // Top queries stack size
        'size' => 15,

        // Stored query lifetime (in seconds)
        'lifetime' => (int) env('DATABASE_QUERIES_PROFILER_TOP_LIFETIME', 10800), // 10800 = 3 hours

        // Exclude queries (sql strings) if they contains next entries
        'exclude' => [

            // ..is enabled?
            'enabled' => false,
            // Array of sub-strings (case insensitive), like: 'select * from "users"'
            'list' => [''],

        ],

    ],

    // Statistics counters statistics
    'counters' => [

        // !!! WARNING !!! WARNING !!! WARNING !!! WARNING !!! WARNING !!! WARNING !!! WARNING !!!
        // !!!               DO NOT USE THIS FEATURE ON PRODUCTION ENVIRONMENT                 !!!
        // !!! WARNING !!! WARNING !!! WARNING !!! WARNING !!! WARNING !!! WARNING !!! WARNING !!!

        // ..is enabled? By default - enabled on non-production environment
        'enabled' => (bool) env(
            'DATABASE_QUERIES_PROFILER_COUNTERS_ENABLED',
            ! Str::contains(env('APP_ENV', 'production'), 'prod')
        ),

    ],

    // Logging settings
    'logging' => [

        // Database queries
        'queries' => [

            // Logging of all database queries into default laravel log
            'all' => [

                // ..is enabled?
                'enabled' => (bool) env('DATABASE_QUERIES_PROFILER_LOGGING_QUERIES_ALL_ENABLED', true),

                // Log entries level (One of: 'debug', 'info', 'notice', 'warning', 'error', 'critical',
                // 'alert', 'emergency')
                // Don`t forget set "APP_LOG_LEVEL=debug" if you use 'debug' level
                'level'   => 'debug',

            ],
        ],

    ],

];
