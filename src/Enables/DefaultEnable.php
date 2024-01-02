<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2024/1/2
 * Time: 17:14
 */

namespace HughCube\Profiler\Enables;

class DefaultEnable
{
    public static function enable(): bool
    {
        return random_int(0, 1000000) < 10;
    }
}
