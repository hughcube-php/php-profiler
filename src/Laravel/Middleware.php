<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2024/1/1
 * Time: 22:58
 */

namespace HughCube\Profiler\Laravel;

use Closure;
use Exception;
use HughCube\Profiler\HProfiler;
use HughCube\Profiler\Profiler;
use Illuminate\Http\Request;

class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->getProfiler()->isEnable('http.middleware', $request)) {
            return $next($request);
        }

        $this->getProfiler()->start();

        $response = $next($request);

        $saveResult = $this->getProfiler()->stop(
            $request->url(),
            $request->query->all(),
            $request->server->all()
        );

        /**
         * Because the saved operation may be asynchronous,
         * you need to wait for the request to complete before releasing the resource
         */
        register_shutdown_function(function () use ($saveResult) {
            $saveResult->await();
        });

        return $response;
    }

    protected function getProfiler(): Profiler
    {
        return HProfiler::getRootProfiler();
    }
}
