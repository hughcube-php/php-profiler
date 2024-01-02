<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 11:36 下午.
 */

namespace HughCube\Profiler\Tests;

use HughCube\Profiler\Laravel\ServiceProvider;
use HughCube\Profiler\Profiler;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @param  Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getProfilerConfig(array $config = []): array
    {
        $defaultConfig = require dirname(__DIR__).'/config/config.php';

        return array_replace($defaultConfig, $config);
    }
}
