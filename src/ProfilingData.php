<?php

namespace HughCube\Profiler;

use HughCube\PUrl\HUrl;

class ProfilingData
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function format(
        string $startedAt,
        array $profile,
        string $url = '',
        array $query = [],
        array $server = [],
        array $env = []
    ): array {

        $startedAtArray = explode('.', $startedAt);

        return [
            'meta' => [
                'url' => $url,
                'get' => $this->skipExcludeKeys($query, $this->getExcludeQueryKeys()),
                'env' => $this->skipExcludeKeys($env, $this->getExcludeEnvKeys()),
                'SERVER' => $this->skipExcludeKeys($server, $this->getExcludeServerKeys()),
                'simple_url' => HUrl::isUrlString($url) ? HUrl::parse($url)->getPath() : $url,
                'request_ts_micro' => [
                    'sec' => intval($startedAtArray[0]),
                    'usec' => intval($startedAtArray[1] ?? 0 ?: 0)
                ],
                'request_ts' => ['sec' => intval($startedAtArray[0]), 'usec' => 0],
                'request_date' => date('Y-m-d', $startedAtArray[0]),
            ],
            'profile' => $profile,
        ];
    }

    public function getExcludeEnvKeys()
    {
        return $this->config->get('profiler.exclude-env', []);
    }

    public function getExcludeServerKeys()
    {
        return $this->config->get('profiler.exclude-server', []);
    }

    public function getExcludeQueryKeys()
    {
        return $this->config->get('profiler.exclude-query', []);
    }

    protected function skipExcludeKeys($values, $excludeKeys): array
    {
        $result = [];

        foreach ($values as $name => $value) {
            if (in_array(strtolower($name), $excludeKeys)) {
                $result[$name] = $value;
            }
        }

        return $result;
    }
}
