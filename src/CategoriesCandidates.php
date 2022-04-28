<?php

namespace Palto;

use Cocur\Slugify\Slugify;
use DateTime;
use Monolog\Handler\ZendMonitorHandler;

class CategoriesCandidates extends Candidates
{
    protected const MODEL =  Model\CategoriesCandidates::class;
    protected const CANDIDATE_CLASS =  CategoryCandidate::class;
}