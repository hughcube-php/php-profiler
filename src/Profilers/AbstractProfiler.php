<?php

namespace HughCube\Profiler\Profilers;

abstract class AbstractProfiler implements ProfilerInterface
{
    protected function combineFlags(array $flags, array $flagMap): int
    {
        $combinedFlag = 0;

        foreach ($flags as $flag) {
            $mappedFlag = array_key_exists($flag, $flagMap) ? $flagMap[$flag] : $flag;
            $combinedFlag |= $mappedFlag;
        }

        return $combinedFlag;
    }
}
