<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2023/12/31
 * Time: 21:48
 */

namespace HughCube\Profiler\Profilers;

interface ProfilerInterface
{
    public function isSupported(): bool;

    public function enable(array $flags = [], array $options = []);

    public function disable(): array;
}
