<?php

namespace HughCube\Profiler\Saver;

interface SaverInterface
{
    public function isSupported(): bool;

    public function save(array $data): SaveResult;
}
