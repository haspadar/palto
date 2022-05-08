<?php

namespace Palto;

class Synonyms
{
    public static function getGropedAll(): array
    {
        $synonyms = \Palto\Model\Synonyms::getAll();
        $grouped = [];
        foreach ($synonyms as $synonym) {
            $grouped[$synonym['category_id']][] = new Synonym($synonym);
        }

        return $grouped;
    }

    public static function findCategory(array $texts): Category
    {
        $synonyms = self::getGropedAll();
        foreach ($synonyms as $categoryId => $categorySynonyms) {
            foreach ($texts as $text) {
                $spacesCount = max(array_map(fn(Synonym $synonym) => $synonym->getSpacesCount(), $synonyms));
                for ($length = $spacesCount + 1; $length >= 1; $length--) {
                    if ($wordsCombinations = self::getWordsCombinations($text, $length)) {
                        foreach ($wordsCombinations as $combination) {
                            if (in_array($combination, array_map(fn(Synonym $synonym) => $synonym->getTitle(), $categorySynonyms))) {
                                return Categories::getById($categoryId);
                            }
                        }
                    }
                }
            }
        }

        return Categories::createUndefined();
    }

    public static function updateUndefinedAds(array $synonyms): int
    {
        $updatedCount = 0;
        foreach ([fn(Ad $ad) => $ad->getTitle(), fn(Ad $ad) => $ad->getText()] as $callback) {
            $undefinedCategories = Categories::getUndefinedAll('level DESC');
            foreach ($undefinedCategories as $undefinedCategory) {
                $limit = 1000;
                $offset = 0;
                foreach (Ads::getAds(null, $undefinedCategory, $limit, $offset) as $ad) {
                    if (self::hasInText($synonyms, $callback($ad))) {
                        Ads::update([
                            'category_id' => $synonyms[0]->getCategory()->getId(),
                            'category_level_1_id' => $synonyms[0]->getCategory()->getLevel() == 1
                                ? $synonyms[0]->getCategory()->getId()
                                : $synonyms[0]->getCategory()->getParentId(),
                            'category_level_2_id' => $synonyms[0]->getCategory()->getLevel() == 2
                                ? $synonyms[0]->getCategory()->getId()
                                : null
                        ], $ad->getId());
                        $updatedCount++;
                    }

                    $offset += $limit;
                }
            }
        }

        return $updatedCount;
    }

    public static function hasInText(array $synonyms, string $text): bool
    {
        for ($length = 5; $length >= 1; $length--) {
            if ($wordsCombinations = self::getWordsCombinations(mb_substr($text, 0, 200), $length)) {
                foreach ($wordsCombinations as $combination) {
                    if (in_array($combination, array_map(fn(Synonym $synonym) => $synonym->getTitle(), $synonyms))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private static function getWordsCombinations(string $text, int $length): array
    {
        $combinations = [];
        $text = mb_strtolower($text);
        $words = array_values(array_filter(explode(' ', strtr($text, ['.' => '', ',' => '', '!' => '', '/']))));
        for ($offset = 0; $offset <= count($words) - $length; $offset++) {
            $combinations[] = trim(implode(' ', array_slice($words, $offset, $length)));
        }

        return $combinations;
    }
}