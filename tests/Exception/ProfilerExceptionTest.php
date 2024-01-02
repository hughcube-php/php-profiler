<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2023/12/31
 * Time: 21:40
 */

namespace HughCube\Profiler\Tests\Exception;

use HughCube\Profiler\Exception\ProfilerException;
use HughCube\Profiler\Tests\TestCase;
use RuntimeException;

class ProfilerExceptionTest extends TestCase
{
    public function testProfilerException()
    {
        $this->expectException(RuntimeException::class);

        throw new ProfilerException('test');
    }
}
