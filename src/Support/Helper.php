<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2024/2/18
 * Time: 14:33.
 */

namespace HughCube\Profiler\Support;

class Helper
{
    public static function dataToDatabaseRow(array $data): array
    {
        $row = [];

        $row['url'] = $data['meta']['url'] ?? null ?: '/';
        $row['simple_url'] = $data['meta']['simple_url'] ?? null ?: '';

        $row['profile'] = $data['profile'] ?: [];

        $row['request_ts'] = intval($data['meta']['request_ts']['sec'] ?? null ?: 0);

        $row['request_ts_micro'] = floatval(sprintf(
            '%d.%d',
            ($data['meta']['request_ts_micro']['sec'] ?? null ?: 0),
            ($data['meta']['request_ts_micro']['usec'] ?? null ?: 0)
        ));

        $row['GET'] = $data['meta']['GET'] ?? null ?: [];
        $row['ENV'] = $data['meta']['ENV'] ?? null ?: [];

        $row['SERVER'] = $data['meta']['SERVER'] ?? null ?: [];
        $row['SERVER'] = array_merge(['REQUEST_TIME' => $row['request_ts']], $row['SERVER']);

        $row['request_date'] = $data['meta']['request_date'] ?? null ?: date('Y-m-d');

        $row['main_wt'] = $row['profile']['main()']['wt'] ?? null ?: 0;
        $row['main_ct'] = $row['profile']['main()']['ct'] ?? null ?: 0;
        $row['main_cpu'] = $row['profile']['main()']['cpu'] ?? null ?: 0;
        $row['main_mu'] = $row['profile']['main()']['mu'] ?? null ?: 0;
        $row['main_pmu'] = $row['profile']['main()']['pmu'] ?? null ?: 0;

        return $row;
    }
}
