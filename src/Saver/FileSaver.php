<?php

namespace HughCube\Profiler\Saver;

class FileSaver extends AbstractSaver implements SaverInterface
{
    /**
     * @var null|false|int
     */
    protected $result = null;

    public function getFile(): ?string
    {
        return $this->config['file'] ?? null;
    }

    public function isSupported(): bool
    {
        $file = $this->getFile();

        if (empty($file)) {
            return false;
        }

        if (is_file($file)) {
            return is_writable($file);
        }

        if (!file_exists($file)) {
            return is_writable(dirname($file));
        }

        return false;
    }

    public function save(array $data): SaveResult
    {
        $file = $this->getFile();

        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        return new SaveResult(
            file_put_contents($file, json_encode($data).PHP_EOL, FILE_APPEND),

            function ($result) {
            },

            function ($result) {
                return 0 > $result;
            }
        );
    }
}
