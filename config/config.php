<?php

use GuzzleHttp\RequestOptions;
use HughCube\Profiler\Profiler;
use HughCube\Profiler\ProfilingFlags;

return [
    'save.handler' => Profiler::SAVER_UPLOAD,

    'save.handler.file' => [
        'file' => storage_path('xhprof/profiler.data'),
    ],

    'save.handler.upload' => [
        'base_uri' => "http://127.0.0.1:9092/api/upload",
        RequestOptions::TIMEOUT => 3.0,
        RequestOptions::CONNECT_TIMEOUT => 3.0,
        RequestOptions::READ_TIMEOUT => 3.0,
        RequestOptions::HTTP_ERRORS => false,
        RequestOptions::PROXY => [
            'http' => '127.0.0.1:8888',
            'https' => '127.0.0.1:8888',
        ],
        RequestOptions::HEADERS => [
            'User-Agent' => null,
            'Authentication' => env('XHPROF_AUTHENTICATION'),
            'X-Fc-Invocation-Type' => env('XHPROF_INVOCATION_TYPE', 'Async'),
        ],
        RequestOptions::VERIFY => false,
    ],

    'profiler.flags' => [
        ProfilingFlags::CPU,
        ProfilingFlags::MEMORY,
        ProfilingFlags::NO_BUILTINS,
        ProfilingFlags::NO_SPANS,
    ],
    'profiler.options' => [],

    'profiler.exclude-env' => [],
    'profiler.exclude-query' => [],
    'profiler.exclude-server' => [],

    'enable' => [
        'default' => 'class'
    ]
];
