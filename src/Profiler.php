<?php

namespace HughCube\Profiler;

use HughCube\Profiler\Profilers\ProfilerInterface;
use HughCube\Profiler\Saver\SaveResult;
use HughCube\Profiler\Saver\SaverInterface;

class Profiler
{
    const SAVER_FILE = 'file';
    const SAVER_UPLOAD = 'upload';

    const PROFILER_XHPROF = 'xhprof';

    /**
     * Profiler configuration.
     *
     * @var Config
     */
    protected $config;

    /**
     * @var null|SaverInterface
     */
    private $saver;

    /**
     * @var null|ProfilerInterface
     */
    private $profiler;

    /**
     * @var null|ProfilingData
     */
    private $profilingData = null;

    /**
     * @var null|string
     */
    protected $startedAt = null;

    /**
     * @param  array|Config  $config
     */
    public function __construct($config)
    {
        if ($config instanceof Config) {
            $this->config = $config;
        } else {
            $this->config = new Config($config);
        }
    }

    private function getProfiler()
    {
        if (null === $this->profiler) {
            $this->profiler = ProfilerFactory::create($this->config);
        }
        return $this->profiler;
    }

    /**
     * @return SaverInterface
     */
    private function getSaver()
    {
        if (null === $this->saver) {
            $this->saver = SaverFactory::create($this->config);
        }
        return $this->saver;
    }

    /**
     * @return ProfilingData
     */
    private function getProfilingData()
    {
        if (null === $this->profilingData) {
            $this->profilingData = new ProfilingData($this->config);
        }
        return $this->profilingData;
    }

    /**
     * @throws \Random\RandomException
     */
    public function isEnable($name, ...$args): bool
    {
        $enables = $this->config->get('enable', []);
        if (isset($enables[$name])) {
            $callable = $enables[$name];
        } elseif (isset($enables['default'])) {
            $callable = $enables['default'];
        } else {
            $callable = function () {
                return random_int(0, 1000000) < $this->config->get('enable.probability', 0);
            };
        }

        return call_user_func_array($callable, $args);
    }

    /**
     * @return $this
     */
    public function start($flags = null, $options = null): Profiler
    {
        if (null !== $this->startedAt) {
            return $this;
        }

        $this->startedAt = sprintf('%.6F', microtime(true));

        $this->getProfiler()->enable(
            ($flags ?? $this->config->get('profiler.flags') ?: []),
            ($options ?? $this->config->get('profiler.options') ?: [])
        );

        return $this;
    }

    /**
     * @return null|SaveResult
     */
    public function stop($url = '', array $query = [], array $server = [], array $env = null)
    {
        if (null === $this->startedAt) {
            return null;
        }

        try {
            $data = $this->getProfilingData()->format(
                $this->startedAt,
                $this->getProfiler()->disable(),
                $url,
                $query,
                $server,
                ($env ?? $_ENV)
            );
            return $this->getSaver()->save($data);
        } finally {
            $this->startedAt = null;
        }
    }
}
