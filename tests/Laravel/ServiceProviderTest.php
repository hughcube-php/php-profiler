<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/18
 * Time: 10:32 下午.
 */

namespace HughCube\Profiler\Tests\Laravel;

use HughCube\Profiler\Profiler;
use HughCube\Profiler\Tests\TestCase;
use HughCube\Profiler\HProfiler;

class ServiceProviderTest extends TestCase
{
    public function testRegister()
    {
        $this->assertInstanceOf(Profiler::class, HProfiler::getProfiler());
    }
}
