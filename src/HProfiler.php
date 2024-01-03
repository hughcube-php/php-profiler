<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/18
 * Time: 10:31 下午.
 */

namespace HughCube\Profiler;

use HughCube\Profiler\Exception\ProfilerException;
use HughCube\Profiler\Saver\SaveResult;

/**
 * @method static bool isEnable($name, ...$args)
 * @method static Profiler start($flags = null, $options = null)
 * @method static null|SaveResult stop($url = '', array $query = [], array $server = [], array $env = null)
 * @method static SaveResult save(string $startedAt, array $profile, string $url = '', array $query = [], array $server = [], array $env = null)
 */
class HProfiler
{
    /**
     * @var null|Profiler
     */
    private static $rootProfiler = null;

    public static function getRootProfiler(): Profiler
    {
        if (!self::$rootProfiler instanceof Profiler) {
            throw new ProfilerException('Profiler is not initialized');
        }
        return self::$rootProfiler;
    }

    public static function setRootProfiler(Profiler $profiler, bool $overwrite = false)
    {
        if ($overwrite || null === self::$rootProfiler) {
            self::$rootProfiler = $profiler;
        }
    }

    public static function __callStatic($name, $arguments)
    {
        return static::getRootProfiler()->$name(...$arguments);
    }
}
