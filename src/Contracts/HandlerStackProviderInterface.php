<?php

namespace HughCube\Profiler\Contracts;

use GuzzleHttp\HandlerStack;

interface HandlerStackProviderInterface
{
    /**
     * 向 HandlerStack 推送中间件
     *
     * @param HandlerStack $handlerStack
     * @return void
     */
    public function push(HandlerStack $handlerStack): void;
}
