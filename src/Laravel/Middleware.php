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
        $requestHost = $request->getHost();

        if (!empty($requestHost)
            && false === filter_var($requestHost, FILTER_VALIDATE_IP)
            && !in_array($requestHost, ['localhost', '127.0.0.1'], true)
        ) {
            return $requestHost;
        }

        if (false !== filter_var($appUrl = config('app.url'), FILTER_VALIDATE_URL)) {
            $appUrlHost = parse_url($appUrl, PHP_URL_HOST);
            if (!empty($appUrlHost) && !in_array($requestHost, ['localhost', '127.0.0.1'], true)) {
                return $appUrlHost;
            }
        }

        return $requestHost ?: null;
    }
}
