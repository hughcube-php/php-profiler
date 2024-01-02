<?php

namespace HughCube\Profiler\Saver;

class FileSaver extends AbstractSaver
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var null|false|int
     */
    protected $result = null;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function isSupported(): bool
    {
        if (is_file($this->file)) {
            return is_writable($this->file);
        }

        if (!file_exists($this->file)) {
            return is_writable(dirname($this->file));
        }

        return false;
    }

    public function save(array $data): SaveResult
    {
        return new SaveResult(
            file_put_contents($this->file, json_encode($data).PHP_EOL, FILE_APPEND),

            function ($result) {
            },

            function ($result) {
                return 0 > $result;
            }
        );
    }
}
