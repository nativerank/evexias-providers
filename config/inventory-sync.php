<?php

return [
    'app_prefix' => 'evxs',

    'queue' => [
        'connection' => env('ITEM_CHANGES_QUEUE_CONNECTION', env('QUEUE_CONNECTION')),
    ],

    'logging' => [
        'channel' => 'stderr',
    ],
];