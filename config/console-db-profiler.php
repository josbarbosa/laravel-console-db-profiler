<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CONSOLE DB PROFILER
    |--------------------------------------------------------------------------
    |
    | Enable profiler for every artisan console command.
    |
    | Note: when you don't want to execute the console profiler for every command,
    | you can change the PROFILER_ENABLED to false and use the debug mode option -vvv
    |
    | Example: PROFILER_ENABLED = false;
    |          "php artisan migrate:status" -> Shows nothing
    |          "php artisan migrate:status -vvv" -> Shows the profiling info
    |
    */
    'enabled'     => env('PROFILER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Environments
    |--------------------------------------------------------------------------
    | Block/Allow the profiling by environment
    | Note: Profiling for the testing environment is not allowed
    |
    */
    'environment' => [
        'staging'    => false,
        'production' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Show total queries execution time
    |--------------------------------------------------------------------------
    */
    'total'       => true,

    /*
    |--------------------------------------------------------------------------
    | Warning for duplicate Queries (Maybe N+1 Problem?)
    |--------------------------------------------------------------------------
    */
    'duplicates'  => true,

    /*
    |--------------------------------------------------------------------------
    | Warnings
    | time in (ms)
    |--------------------------------------------------------------------------
    */
    'threshold'   => [
        'slow_query'    => 1000,
        'total_queries' => 20000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Show typologies
    | How many inserts, deletes, selects, updates, alter ...
    |--------------------------------------------------------------------------
    */
    'typologies'  => false,

    /*
    |--------------------------------------------------------------------------
    | Show hints for common mistakes
    |--------------------------------------------------------------------------
    */
    'hints'       => false,

    /*
    |--------------------------------------------------------------------------
    | Limit the number of queries shown in console
    | Queries are shown by execution order
    |--------------------------------------------------------------------------
    */
    'limit'       => 100,

    /*
    |--------------------------------------------------------------------------
    | Save Queries to a log file
    |--------------------------------------------------------------------------
    */
    'log'         => [
        'enabled' => false,
        'options' => [
            'append' => false,
            'path'   => storage_path('logs/query.log'),
        ],
    ],
];
