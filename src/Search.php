<?php

namespace Palto;

use Monolog\Logger;

require_once 'sphinxapi.php';

class Search
{
    const REINDEX_SCRIPT = 'reindex.php';

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

    public static function reIndex(Logger $logger): void
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $commandOutput = system("indexer --all --rotate");
        $logger->info($commandOutput);
        $executionTime->end();
        $logger->info('Sphinx reIndexed for ' . $executionTime->get());
    }
}