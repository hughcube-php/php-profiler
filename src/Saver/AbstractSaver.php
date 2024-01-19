<?php

namespace HughCube\Profiler\Saver;

abstract class AbstractSaver implements SaverInterface
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }
}
