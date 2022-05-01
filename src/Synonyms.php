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

    public static function updateUndefinedAds(array $synonyms, int $newCategoryId)
    {
        $updatedCount = 0;
        $undefinedCategories = Categories::getUndefinedAll();
        foreach ($undefinedCategories as $undefinedCategory) {
            $limit = 1000;
            $offset = 0;
            foreach (Ads::getAds(null, $undefinedCategory, $limit, $offset) as $ad) {
                $texts = [$ad->getTitle(), mb_substr($ad->getText(), 0, 200)];
                $foundAll = self::find($texts, $undefinedCategory);
                $offset += $limit;
            }
        }
    }

    private static function find(array $texts, ?Category $category): ?Category
    {
        foreach ($texts as $text) {
            for ($length = 5; $length >= 1; $length--) {
                if ($wordsCombinations = self::getWordsCombinations($text, $length)) {
                    foreach ($wordsCombinations as $combinationKey => $combination) {
                        if ($found = Categories::findByTitle($combination, $category)) {
                            return $found;
                        }
                    }
                }
            }
        }

        return Categories::createUndefined($category);
    }

    private function getWordsCombinations(string $text, int $length): array
    {
        $combinations = [];
        $text = mb_strtolower($text);
        $words = array_values(array_filter(explode(' ', strtr($text, ['.' => '', ',' => '', '!' => '']))));
        for ($offset = 0; $offset <= count($words) - $length; $offset++) {
            $combinations[] = trim(implode(' ', array_slice($words, $offset, $length)));
        }

        return $combinations;
    }
}