<?php

namespace HughCube\Profiler;

use HughCube\Profiler\Exception\ProfilerException;
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
                return new Saver\FileSaver($config->get('save.handler.file', []));
            },

            Profiler::SAVER_UPLOAD => function (Config $config) {
                return new Saver\UploadSaver($config->get('save.handler.upload', []));
            },

            Profiler::SAVER_CALLABLE => function (Config $config) {
                return new Saver\CallableSaver($config->get('save.handler.callable', []));
            },

            Profiler::SAVER_LARAVEL => function (Config $config) {
                return new Saver\LaravelSaver($config->get('save.handler.laravel', []));
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
