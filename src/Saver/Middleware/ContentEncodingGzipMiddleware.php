<?php

namespace HughCube\Profiler\Saver\Middleware;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Utils;
use HughCube\Profiler\Contracts\HandlerStackProviderInterface;
use Psr\Http\Message\RequestInterface;

/**
 * 请求体 Gzip 压缩中间件
 *
 * 使用标准 Content-Encoding: gzip 头压缩请求体
 *
 * 虽然请求体压缩不如响应压缩常见,但符合 HTTP 标准
 * 服务器端需要支持 gzip 解压缩才能正常工作
 *
 * 实际应用案例: Python Requests, Java Spring, OpenTelemetry 等
 */
class ContentEncodingGzipMiddleware implements HandlerStackProviderInterface
{
    /**
     * 压缩阈值 (字节)
     *
     * @var int
     */
    protected $threshold;

    /**
     * 压缩级别 (1-9)
     *
     * @var int
     */
    protected $level;

    /**
     * @param int $threshold 压缩阈值(字节),默认 1KB
     * @param int $level 压缩级别(1-9),默认 6
     */
    public function __construct(int $threshold = 1024, int $level = 6)
    {
        $this->threshold = $threshold;
        $this->level = max(1, min(9, $level));
    }

    /**
     * 向 HandlerStack 推送中间件
     *
     * @param HandlerStack $handlerStack
     * @return void
     */
    public function push(HandlerStack $handlerStack): void
    {
        if (!function_exists('gzencode')) {
            return;
        }

        $handlerStack->push(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $body = $request->getBody();
                $bodySize = $body->getSize();

                if (null === $bodySize || $bodySize <= $this->threshold) {
                    return $handler($request, $options);
                }

                $body->rewind();
                $contents = $body->getContents();
                $compressed = gzencode($contents, $this->level);

                if (false === $compressed || strlen($compressed) >= strlen($contents)) {
                    $body->rewind();
                    return $handler($request, $options);
                }

                $request = $request
                    ->withoutHeader('Content-Type')
                    ->withBody(Utils::streamFor($compressed))
                    ->withHeader('X-Content-Encoding', 'gzip')
                    ->withHeader('Content-Length', strlen($compressed));

                return $handler($request, $options);
            };
        });
    }
}
