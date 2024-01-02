<?php

namespace HughCube\Profiler\Tests;

use HughCube\Profiler\Profiler;

class ProfilerTest extends TestCase
{
    public function testProfiler()
    {
        $profiler = new Profiler($this->getProfilerConfig());

        $profiler->start();

        $this->assertTrue(true);

        $profiler->stop();
    }
}
