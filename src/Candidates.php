<?php

namespace Palto;

use Cocur\Slugify\Slugify;
use DateTime;
use Monolog\Handler\ZendMonitorHandler;

class Candidates
{
    protected const MODEL =  Model\CategoriesCandidates::class;
    protected const CANDIDATE_CLASS =  CategoryCandidate::class;

    /**
     * @return CategoryCandidate[]
     */
    public static function getLeafs(int $limit = 0): array
    {
        return array_map(
            fn($leaf) => new static::CANDIDATE_CLASS($leaf),
            static::MODEL::getLeafs($limit)
        );
    }

}