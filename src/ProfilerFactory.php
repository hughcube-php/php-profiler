<?php

namespace HughCube\Profiler;

use HughCube\Profiler\Exception\ProfilerException;
use HughCube\Profiler\Profilers\ProfilerInterface;

/**
 * @internal
 */
class ProfilerFactory
{
    public static function create(Config $config): ProfilerInterface
    {
        $adapters = [
            Profiler::PROFILER_XHPROF => function () {
                return new Profilers\XHProfProfiler();
            },
        ];

        if ($config->offsetExists('profiler')) {
            $profiler = $config->offsetGet('profiler');
            if (!isset($adapters[$profiler])) {
                throw new ProfilerException(sprintf('Specified profiler \'%s\' is not supported', $profiler));
            }

            /** @var ProfilerInterface $adapter */
            $adapter = $adapters[$profiler]();
            if (!$adapter->isSupported()) {
                throw new ProfilerException(sprintf('Specified profiler \'%s\' is not supported', $profiler));
            }

            return $adapter;
        }

        foreach ($adapters as $factory) {
            $adapter = $factory();
            if ($adapter->isSupported()) {
                return $adapter;
            }
        }

        throw new ProfilerException('Unable to create profiler: No suitable profiler found');
    }
}
