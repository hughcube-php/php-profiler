<?php

namespace HughCube\Profiler\Saver\Middleware;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Utils;
use HughCube\Profiler\Contracts\HandlerStackProviderInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Content-Encoding Gzip 压缩中间件
 *
 * 使用标准 HTTP Content-Encoding: gzip 头进行压缩
 * 服务器端会自动解压,无需额外处理
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
                    ->withBody(Utils::streamFor($compressed))
                    ->withHeader('Content-Encoding', 'gzip')
                    ->withoutHeader('Content-Length');

                return $handler($request, $options);
            };
        });
    }
}
