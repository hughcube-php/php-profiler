<?php

namespace HughCube\Profiler\Saver;

use GuzzleHttp\RequestOptions;
use HughCube\GuzzleHttp\Client;
use HughCube\GuzzleHttp\LazyResponse;

class UploadSaver implements SaverInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var null|LazyResponse
     */
    protected $result = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function isSupported(): bool
    {
        return $this->client instanceof Client;
    }

    public function save(array $data): SaveResult
    {
        return new SaveResult(
            $this->client->requestLazy('POST', '', [RequestOptions::JSON => $data]),

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
