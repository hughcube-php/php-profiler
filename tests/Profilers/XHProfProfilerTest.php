<?php

namespace HughCube\Profiler\Tests\Profilers;

use HughCube\Profiler\Profilers\XHProfProfiler;
use HughCube\Profiler\Tests\TestCase;

class XHProfProfilerTest extends TestCase
{
    public function testIsSupported()
    {
        $profiler = new XHProfProfiler();
        $this->assertTrue($profiler->isSupported());
    }

    public function testXHProf()
    {
        $profiler = new XHProfProfiler();
        $this->assertTrue(is_null($profiler->enable()));

        $this->assertTrue(true);

        $this->assertTrue(is_array($profiler->disable()));

        $profiler = new XHProfProfiler();
        $this->assertTrue(is_null($profiler->enable()));
        $this->assertTrue(is_array($profiler->disable()));
    }
}
