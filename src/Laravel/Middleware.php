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
use HughCube\PUrl\Url as PUrl;
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
        if (!$this->isEnable($request)) {
            return $next($request);
        }

        $this->getProfiler()->start();

        $response = $next($request);

        $saveResult = $this->getProfiler()->stop(
            ($request->getPathInfo() ?: '/'),
            $request->query->all(),
            array_merge($request->server->all(), array_filter(['SERVER_NAME' => $this->getServerName($request)]))
        );

        $saveResult->await();

        return $response;
    }

    /**
     * @throws \Random\RandomException
     */
    protected function isEnable(Request $request): bool
    {
        return $this->getProfiler()->isEnable('http.middleware', $request);
    }

    protected function getProfiler(): Profiler
    {
        return HProfiler::getRootProfiler();
    }

    protected function getServerName(Request $request): ?string
    {
        $host = $request->getHost();

        if (!empty($host)
            && false === filter_var($host, FILTER_VALIDATE_IP)
            && !in_array($host, ['localhost', '127.0.0.1'], true)
        ) {
            return $host;
        }

        $appUrl = PUrl::parse(config('app.url'));
        if ($appUrl instanceof PUrl
            && !$appUrl->matchHost('localhost')
            && !$appUrl->matchHost('127.0.0.1')
        ) {
            return $appUrl->getHost();
        }

        return null;
    }
}
