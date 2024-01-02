<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/18
 * Time: 10:32 下午.
 */

namespace HughCube\Profiler\Laravel;

use HughCube\Profiler\HProfiler;
use HughCube\Profiler\Profiler;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the provider.
     * @throws BindingResolutionException
     */
    public function register()
    {
        /** @var Repository $config */
        $config = $this->app->make('config');

        HProfiler::setRootProfiler(new Profiler($config->get('xhprof')));
    }
}
