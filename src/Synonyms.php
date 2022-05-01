<?php

namespace Palto;

class Synonyms
{
    public static function getAll(): array
    {
        $synonyms = \Palto\Model\Synonyms::getAll();
        $grouped = [];
        foreach ($synonyms as $synonym) {
            $grouped[$synonym['category_id']][] = $synonym['title'];
        }

        return $grouped;
    }
}