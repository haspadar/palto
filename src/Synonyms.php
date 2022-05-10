<?php

namespace Palto;

class Synonyms
{
    const FIND_AND_MOVE_SCRIPT = 'bin/find_and_move.php';

    public static function getGropedAll(): array
    {
        $synonyms = Model\Synonyms::getAll();
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

    /**
     * @param Category[] $categories
     * @return int
     */
    public static function findAndMoveAds(array $categories): int
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $movedAdsCount = 0;
        $gropedSynonyms = Synonyms::getGropedAll();
        $iterator = 0;
        foreach (['title', 'text'] as $adField) {
            foreach ($gropedSynonyms as $categoryId => $synonyms) {
                $toCategory = Categories::getById($categoryId);
                Logger::debug('Поиск по синонимам "'
                    . $toCategory->groupSynonyms($synonyms)
                    . '" ('
                    . (++$iterator)
                    . '/'
                    . count($gropedSynonyms)
                    . ')'
                );
                $movedAdsCount += self::moveCategoryAds($toCategory, $synonyms, $adField, $categories);
            }
        }

        $executionTime->end();
        Logger::info('Moved ' . $movedAdsCount . ' ads for ' . $executionTime->get());
        
        return $movedAdsCount;
    }
    
    public static function updateCategory(Category $category, array $synonymTitles): void
    {
        $existsSynonymTitles = Model\Synonyms::getTitles($category->getId());
        $removableSynonymTitles = array_diff($existsSynonymTitles, $synonymTitles);
        foreach ($removableSynonymTitles as $removableSynonymTitle) {
            Model\Synonyms::remove($removableSynonymTitle, $category->getId());
        }

        $addingSynonymTitles = array_diff($synonymTitles, $existsSynonymTitles);
        foreach ($addingSynonymTitles as $addingSynonymTitle) {
            Model\Synonyms::add($addingSynonymTitle, $category->getId());
        }
    }

    public static function getByTitle(string $title): array
    {
        return Model\Synonyms::getByTitle($title);
    }

    /**
     * @param Category $toCategory
     * @param Synonym[] $synonyms
     * @param string $adField
     * @param Category[] $categories
     * @return int
     */
    public static function moveCategoryAds(Category $toCategory, array $synonyms, string $adField, array $categories): int
    {
        $movedAdsCount = 0;
        if ($synonyms) {
            foreach ($categories as $key => $category) {
                $limit = 1000;
                $offset = 0;
                Logger::debug('Поиск объявлений в "' . $category->getTitle() . '" (' . ($key + 1) . '/' . count($categories) . ')');
                while ($ads = Ads::getAds(null, $category, $limit, $offset)) {
                    foreach ($ads as $ad) {
                        if (self::hasAdSynonyms($ad, $synonyms, $adField)) {
                            Logger::debug('Найдено объявление!');
                            self::moveAd($ad, $toCategory);
                            $movedAdsCount++;
                        }
                    }

                    $offset += $limit;
                }
            }   
        }

        return $movedAdsCount;
    }

    /**
     * @param Ad $ad
     * @param Synonym[] $synonyms
     * @param string $adField
     * @return bool
     */
    public static function hasAdSynonyms(Ad $ad, array $synonyms, string $adField): bool
    {
        $spacesCount = max(array_map(fn(Synonym $synonym) => $synonym->getSpacesCount(), $synonyms));
        for ($length = $spacesCount + 1; $length >= 1; $length--) {
            $adMethod = 'get' . ucfirst($adField);
            if ($wordsCombinations = self::getWordsCombinations(mb_substr($ad->$adMethod(), 0, 200), $length)) {
                foreach ($wordsCombinations as $combination) {
                    if (in_array(
                        mb_strtolower($combination),
                        array_map(
                            fn(Synonym $synonym) => mb_strtolower($synonym->getTitle()),
                            $synonyms)
                        )
                    ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private static function moveAd(Ad $ad, Category $category): void
    {
        Ads::update([
            'category_id' => $category->getId(),
            'category_level_1_id' => $category->getLevel() == 1
                ? $category->getId()
                : $category->getParentId(),
            'category_level_2_id' => $category->getLevel() == 2
                ? $category->getId()
                : null
        ], $ad->getId());
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