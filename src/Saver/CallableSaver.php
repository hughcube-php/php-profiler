<?php

namespace HughCube\Profiler\Saver;

class CallableSaver extends AbstractSaver implements SaverInterface
{
    /**
     * @var null|false|int
     */
    protected $result = null;

    public function isSupported(): bool
    {
        if (isset($this->config['is-supported']) && is_callable($this->config['is-supported'])) {
            return call_user_func($this->config['is-supported']);
        }

        if (isset($this->config['is-supported'])) {
            return boolval($this->config['is-supported']);
        }

        return true;
    }

    public function save(array $data): SaveResult
    {
        $callable = $this->parseCallback($this->config['callable']);

        return new SaveResult(
            $callable($data),
            function ($result) {
                return is_callable($result) ? $result() : null;
            },
            function ($result) {
                return true;
            }
        );
    }

    public static function parseCallback($callback, $default = null)
    {
        return is_callable($callback) ? $callback : function () use($default) {
            return $default;
        };
    }
}
