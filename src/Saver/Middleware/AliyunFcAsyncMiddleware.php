<?php

namespace HughCube\Profiler\Saver\Middleware;

use GuzzleHttp\HandlerStack;
use HughCube\Profiler\Contracts\HandlerStackProviderInterface;
use Psr\Http\Message\RequestInterface;

/**
 * 阿里云函数计算异步调用中间件
 *
 * 当请求体小于等于指定阈值时,自动添加 X-Fc-Invocation-Type: Async 头以使用异步调用
 *
 * 限制说明:
 * - 同步调用: 最大请求体 32 MB
 * - 异步调用: 最大请求体 128 KB
 *
 * @see https://help.aliyun.com/zh/functioncompute/fc-2-0/user-guide/overview-36
 */
class AliyunFcAsyncMiddleware implements HandlerStackProviderInterface
{
    /**
     * 请求体大小阈值 (字节)
     * 默认值为异步最大限制减去安全边界
     *
     * @var int
     */
    protected $threshold;

    /**
     * 异步调用最大限制 (字节)
     * 阿里云函数计算异步调用最大支持 128 KB
     *
     * @var int
     */
    const ASYNC_MAX_SIZE = 128 * 1024; // 128 KB

    /**
     * 安全边界 (字节)
     * 预留空间以应对 HTTP 头、编码差异等额外开销
     *
     * @var int
     */
    const SAFETY_MARGIN = 2 * 1024; // 2 KB

    /**
     * @param int|null $threshold 请求体大小阈值(字节),默认为 126 KB (128KB - 2KB 安全边界)
     */
    public function __construct(?int $threshold = null)
    {
        $this->threshold = $threshold ?? (self::ASYNC_MAX_SIZE - self::SAFETY_MARGIN);
    }

    /**
     * 向 HandlerStack 推送中间件
     *
     * @param HandlerStack $handlerStack
     * @return void
     */
    public function push(HandlerStack $handlerStack): void
    {
        $handlerStack->push(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $bodySize = $this->getBodySize($request);

                if (null !== $bodySize && $bodySize <= $this->threshold) {
                    $request = $request->withHeader('X-Fc-Invocation-Type', 'Async');
                }

                return $handler($request, $options);
            };
        });
    }

    /**
     * 获取请求体大小
     *
     * @param RequestInterface $request
     * @return int|null 请求体大小(字节),无法确定时返回 null
     */
    protected function getBodySize(RequestInterface $request): ?int
    {
        if ($request->hasHeader('Content-Length')) {
            return intval($request->getHeaderLine('Content-Length'));
        }

        $body = $request->getBody();

        $size = $body->getSize();
        if (null !== $size) {
            return $size;
        }

        if ($body->isSeekable()) {
            $position = $body->tell();
            $body->rewind();
            $size = strlen($body->getContents());
            $body->seek($position);
            return $size;
        }

        return null;
    }
}
