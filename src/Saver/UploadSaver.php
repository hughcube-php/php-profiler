<?php

namespace HughCube\Profiler\Saver;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use HughCube\GuzzleHttp\Client;
use HughCube\GuzzleHttp\HttpClientTrait;
use HughCube\GuzzleHttp\LazyResponse;
use HughCube\Profiler\Contracts\HandlerStackProviderInterface;
use Psr\Http\Message\RequestInterface;

class UploadSaver extends AbstractSaver implements SaverInterface
{
    use HttpClientTrait;

    /**
     * @var null|LazyResponse
     */
    protected $result = null;

    protected function createHttpClient(): Client
    {
        $config = $this->config;

        $config['handler'] = $handler = HandlerStack::create();

        /** 证书认证 */
        $handler->push(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                /** When you forcibly change the host using HTTPS, HTTPS authentication must be disabled. */
                if ('https' === $request->getUri()->getScheme()
                    && $request->getUri()->getHost() !== $request->getHeaderLine('Host')
                ) {
                    $options[RequestOptions::VERIFY] = false;
                }

                return $handler($request, $options);
            };
        });

        /** 应用自定义 HandlerStack 提供者 */
        $this->applyHandlerStackProviders($handler);

        return new Client($config);
    }

    /**
     * 应用配置的 HandlerStack 提供者
     *
     * @param HandlerStack $handlerStack
     * @return void
     */
    protected function applyHandlerStackProviders(HandlerStack $handlerStack): void
    {
        $providers = $this->config['middlewares'] ?? [];

        if (empty($providers)) {
            return;
        }

        $providers = is_array($providers) ? $providers : [$providers];

        foreach ($providers as $provider) {
            if (is_string($provider)) {
                $provider = new $provider();
            }

            if (is_callable($provider)) {
                call_user_func($provider, $handlerStack);
            } elseif ($provider instanceof HandlerStackProviderInterface) {
                $provider->push($handlerStack);
            }
        }
    }

    public function isSupported(): bool
    {
        return $this->getHttpClient() instanceof Client;
    }

    public function save(array $data): SaveResult
    {
        return new SaveResult(
            $this->getHttpClient()->requestLazy('POST', '', [RequestOptions::JSON => $data]),

            function ($result) {
                if ($result instanceof LazyResponse) {
                    $result->getStatusCode();
                }
            },

            function ($result) {
                if (!$result instanceof LazyResponse) {
                    return false;
                }
                return 200 <= $result->getStatusCode() && 300 > $result->getStatusCode();
            }
        );
    }
}
