<?php

namespace HughCube\Profiler\Saver;

use HughCube\Profiler\Exception\SaverException;
use HughCube\Profiler\Support\Helper;
use Illuminate\Support\Facades\DB;

class LaravelSaver extends AbstractSaver implements SaverInterface
{
    /**
     * @var null|false|int
     */
    protected $result = null;

    public function isSupported(): bool
    {
        return true;
    }

    public function save(array $data): SaveResult
    {
        return new SaveResult(
            call_user_func(function () use ($data) {
                $class = $this->config['model'] ?? null;
                if (!empty($class)) {
                    return $this->saveToModel($class, Helper::dataToDatabaseRow($data));
                }

                $table = $this->config['table'] ?? null;
                $connection = $this->config['connection'] ?? null;
                if (!empty($table) && !empty($connection)) {
                    return $this->saveToConnection($connection, $table, Helper::dataToDatabaseRow($data));
                }

                throw new SaverException('No model or table and connection config found!');
            }),
            function ($result) {
            },
            function ($result) {
                return true;
            }
        );
    }

    protected function saveToModel($class, array $row): bool
    {
        $model = new $class();

        foreach ($row as $name => $value) {
            $model->$name = $value;
        }

        if (!$model->save()) {
            throw new SaverException('Save failed!');
        }

        return true;
    }

    protected function saveToConnection($name, $table, array $row): bool
    {
        $connection = DB::connection($name);

        if (!$connection->table($table)->insert(
            array_map(function ($value) {
                return is_array($value) ? json_encode($value) : $value;
            }, $row)
        )) {
            throw new SaverException('Insert failed!');
        }

        return true;
    }
}
