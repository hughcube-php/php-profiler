<?php

namespace HughCube\Profiler\Profilers;

use HughCube\Profiler\ProfilingFlags;

class XHProfProfiler extends AbstractProfiler
{
    public function isSupported(): bool
    {
        return extension_loaded('xhprof');
    }

    /**
     * @see https://www.php.net/manual/en/function.xhprof-enable.php
     */
    public function enable(array $flags = [], array $options = [])
    {
        xhprof_enable($this->combineFlags($flags, $this->getProfileFlagMap()), $options);
    }

    /**
     * @see https://www.php.net/manual/en/function.xhprof-disable.php
     */
    public function disable(): array
    {
        return xhprof_disable() ?: ['main()' => ['ct' => 0, 'wt' => 0, 'cpu' => 0, 'mu' => 0, 'pmu' => 0]];
    }

    /**
     * @see https://www.php.net/manual/en/xhprof.constants.php
     */
    private function getProfileFlagMap(): array
    {
        /*
         * This is disabled on PHP 5.5+ as it causes a segfault
         *
         * @see https://github.com/perftools/xhgui-collector/commit/d1236d6422bfc42ac212befd0968036986885ccd
         */
        $noBuiltins = PHP_MAJOR_VERSION === 5 && PHP_MINOR_VERSION > 4 ? 0 : XHPROF_FLAGS_NO_BUILTINS;

        return array(
            ProfilingFlags::CPU => XHPROF_FLAGS_CPU,
            ProfilingFlags::MEMORY => XHPROF_FLAGS_MEMORY,
            ProfilingFlags::NO_BUILTINS => $noBuiltins,
            ProfilingFlags::NO_SPANS => 0,
        );
    }
}
