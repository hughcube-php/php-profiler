<?php

namespace HughCube\Profiler;

use HughCube\GuzzleHttp\Client;
use HughCube\Profiler\Config;
use HughCube\Profiler\Exception\ProfilerException;
use HughCube\Profiler\Profiler;
use HughCube\Profiler\Saver;
use HughCube\Profiler\Saver\SaverInterface;

/**
 * @internal
 */
class SaverFactory
{
    public static function create(Config $config): SaverInterface
    {
        $adapters = [
            Profiler::SAVER_FILE => function (Config $config) {
                $config = $config->get('save.handler.file', []);
                return new Saver\FileSaver($config['file'] ?? null);
            },

            Profiler::SAVER_UPLOAD => function (Config $config) {
                $client = new Client($config->get('save.handler.upload', []));
                return new Saver\UploadSaver($client);
            },
        ];

        if ($config->offsetExists('save.handler')) {
            $handler = $config->offsetGet('save.handler');
            if (!isset($adapters[$handler])) {
                throw new ProfilerException(sprintf('Specified handler \'%s\' is not supported', $handler));
            }

            /** @var SaverInterface $adapter */
            $adapter = $adapters[$handler]($config);
            if (!$adapter->isSupported()) {
                throw new ProfilerException(sprintf('Specified handler \'%s\' is not supported', $handler));
            }

            return $adapter;
        }

        foreach ($adapters as $factory) {
            $adapter = $factory($config);
            if ($adapter->isSupported()) {
                return $adapter;
            }
        }

        throw new ProfilerException('Unable to create saver: No suitable saver found');
    }
}
