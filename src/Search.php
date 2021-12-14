<?php

namespace Palto;

require_once 'sphinxapi.php';

class Search
{
    public static function getIds(array $found): array
    {
        if ($found && isset($found['matches'])) {
            return array_filter(array_keys($found['matches']));
        }

        return [];
    }

    public static function find(string $query, string $index, int $offset, int $limit): array
    {
        $sphinx = new \SphinxClient();
        $sphinx->SetSortMode(SPH_SORT_ATTR_DESC, 'create_timestamp');
        $sphinx->SetLimits($offset, $limit);

        return $sphinx->Query($query, $index) ?: [];
    }

    public static function getCount(array $found): int
    {
        return $found['total_found'] ?? 0;
    }

}