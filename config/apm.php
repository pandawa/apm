<?php

use Pandawa\Apm\Watcher\JobWatcher;
use Pandawa\Apm\Watcher\MessageWatcher;
use Pandawa\Apm\Watcher\QueryWatcher;
use Pandawa\Apm\Watcher\RedisWatcher;
use Pandawa\Apm\Watcher\RequestWatcher;

return [
    /**
     * Enable APM
     */
    'enabled' => env('APM_ENABLED', true),

    'default' => env('APM_DEFAULT', 'log'),

    'watchers' => [
        RequestWatcher::class => [
            'enabled' => env('APM_ENABLE_REQUEST_WATCHER', true),
            'type'    => 'request',
        ],
        QueryWatcher::class   => [
            'enabled' => env('APM_ENABLE_QUERY_WATCHER', true),
            'type'    => 'db',
        ],
        RedisWatcher::class   => [
            'enabled' => env('APM_ENABLE_REDIS_WATCHER', true),
            'type'    => 'redis',
        ],
        JobWatcher::class     => [
            'enabled' => env('APM_ENABLE_JOB_WATCHER', true),
            'type'    => 'job',
        ],
        MessageWatcher::class => env('APM_ENABLE_MESSAGE_WATCHER', true),
    ],
];
