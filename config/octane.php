<?php

use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\TickTerminated;
use Laravel\Octane\Events\WorkerErrorOccurred;
use Laravel\Octane\Events\WorkerStopping;
use Laravel\Octane\Listeners\EnsureUploadedFilesAreValid;
use Laravel\Octane\Listeners\StopWorkerIfNecessary;
use Laravel\Octane\Listeners\FlushTemporaryContainerInstances;
use Laravel\Octane\Listeners\DisconnectFromDatabases;
use Laravel\Octane\Listeners\ReportException;
use Laravel\Octane\Listeners\CollectGarbage;

return [
    'server' => env('OCTANE_SERVER', 'swoole'),

    'https' => env('OCTANE_HTTPS', false),

    'listeners' => [
        WorkerStarting::class => [
            EnsureUploadedFilesAreValid::class,
        ],

        RequestReceived::class => [
            EnsureUploadedFilesAreValid::class,
            StopWorkerIfNecessary::class,
        ],

        RequestHandled::class => [
            // FlushTemporaryContainerInstances::class,
        ],

        RequestTerminated::class => [
            DisconnectFromDatabases::class,
        ],

        TaskReceived::class => [
            EnsureUploadedFilesAreValid::class,
            StopWorkerIfNecessary::class,
        ],

        TaskTerminated::class => [
            FlushTemporaryContainerInstances::class,
            DisconnectFromDatabases::class,
        ],

        TickReceived::class => [
            StopWorkerIfNecessary::class,
        ],

        TickTerminated::class => [
            FlushTemporaryContainerInstances::class,
            DisconnectFromDatabases::class,
        ],

        WorkerErrorOccurred::class => [
            ReportException::class,
            StopWorkerIfNecessary::class,
        ],

        WorkerStopping::class => [
            DisconnectFromDatabases::class,
        ],
    ],

    'warm' => [
        CollectGarbage::class,
    ],

    'intervals' => [
        'collect_garbage' => 600,
    ],

    'max_execution_time' => 30,

    'max_requests' => 500,

    'state' => [
        'tables' => [],
    ],

    'cache' => [
        'rows' => 1000,
        'bytes' => 10000,
    ],

    'watch' => [
        'directories' => [
            'app',
            'bootstrap',
            'config',
            'database',
            'public/**/*.php',
            'resources/**/*.php',
            'routes',
            'composer.lock',
            '.env',
        ],
        'exclude' => [
            'storage',
            'vendor',
        ],
    ],

    'flush_entries' => [
        'auth.guards.*',
        'auth.password_brokers.*',
        'config.cache',
        'cookie',
        'database.connections.*',
        'database.schema',
        'queue.connection',
        'session.*',
        'view.compiled',
        'view.expires',
        'view.finder',
    ],

    'onBoot' => function () {
        app(\App\Services\PrivateRelayService::class)->initializeTrie();
    },
]; 