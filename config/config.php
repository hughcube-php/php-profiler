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
        RequestOptions::PROXY => env('PROFILER_HTTP_PROXY'),
        RequestOptions::HEADERS => [
            'User-Agent' => null,
            'Authentication' => env('PROFILER_AUTHENTICATION'),
            'X-Fc-Invocation-Type' => env('PROFILER_INVOCATION_TYPE', 'Async'),
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

    'enable.probability' => 10,
    'enable' => [
        'default' => true
    ]
];
