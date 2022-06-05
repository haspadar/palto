<?php

namespace Palto;

use ICanBoogie\Inflector;

class Synonyms
{
    const FIND_AND_MOVE_SCRIPT = 'bin/find_and_move.php';

    /**
     * @return Synonym[]
     */
    public static function getAll(): array
    {
        return array_map(fn(array $synonym) => new Synonym($synonym), (new Model\Synonyms)->getAll());
    }

    public static function findCategory(Ad $ad): Category
    {
        $synonyms = self::getAll();
        foreach ($synonyms as $synonym) {
            for ($length = $synonym->getSpacesCount() + 1; $length >= 1; $length--) {
                foreach ([$ad->getTitle(), $ad->getText(200)] as $text) {
                    if (self::hasSynonym($text, $synonym)) {
                        return $synonym->getCategory();
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
        $synonyms = Synonyms::getAll();
        $movedAdsCount = self::moveCategoryAds($synonyms, $categories);
        $executionTime->end();
        Logger::info('Moved ' . $movedAdsCount . ' ads for ' . $executionTime->get());
        
        return $movedAdsCount;
    }
    
    public static function updateCategory(Category $category, array $synonymTitles): void
    {
        $existsSynonymTitles = (new Model\Synonyms)->getTitles($category->getId());
        $removableSynonymTitles = array_diff($existsSynonymTitles, $synonymTitles);
        foreach ($removableSynonymTitles as $removableSynonymTitle) {
            (new Model\Synonyms)->removeBy($removableSynonymTitle, $category->getId());
        }

        $addingSynonymTitles = array_diff($synonymTitles, $existsSynonymTitles);
        foreach ($addingSynonymTitles as $addingSynonymTitle) {
            (new Model\Synonyms)->add(['title' => $addingSynonymTitle, 'category_id' => $category->getId()]);
        }
    }

    public static function getByTitle(string $title): ?Synonym
    {
        $found = (new Model\Synonyms)->getByTitle($title);

        return $found ? new Synonym($found) : null;
    }

    /**
     * @param Synonym[] $synonyms
     * @param Category[] $categories
     * @return int
     */
    public static function moveCategoryAds(array $synonyms, array $categories): int
    {
        $movedAdsCount = 0;
        if ($synonyms) {
            $limit = 1000;
            $offset = 0;
            $adsCount = Ads::getAdsCount(array_map(fn(Category $category)=> $category->getId(), $categories));
            while ($ads = Ads::getFields($categories, ['id', 'title', 'text', 'category_id', 'region_id', 'deleted_time'], $limit, $offset)) {
                Logger::info('Search in ' . ($offset + count($ads)) . '/' . $adsCount . ' ads...');
                foreach ($ads as $adFields) {
                    $ad = new Ad($adFields, [], []);
                    foreach ($synonyms as $synonym) {
                        foreach ([$ad->getTitle(), $ad->getText(200)] as $key => $text) {
                            if (self::hasSynonym($text, $synonym)) {
                                self::moveAd($ad, $synonym->getCategory(), $key ? 'text': 'title', $synonym);
                                $movedAdsCount++;
                            }
                        }
                    }
                }

                $offset += $limit;
            }
        }

        return $movedAdsCount;
    }

    public static function add(array $synonyms, int $categoryId): void
    {
        foreach ($synonyms as $synonym) {
            (new Model\Synonyms)->add(['title' => $synonym, 'category_id' => $categoryId]);
        }
    }

    public static function generateForms(array $forms): array
    {
        $combinations = array_map(fn($form) => mb_strtolower($form), $forms);
        $inflector = Inflector::get('en');
        foreach ($forms as $uniqueForm) {
            $plural = $inflector->pluralize($uniqueForm);
            if ($plural != $uniqueForm) {
                $combinations[] = $plural;
            }

            $singular = $inflector->singularize($uniqueForm);
            if ($singular != $uniqueForm) {
                $combinations[] = $singular;
            }
        }

        return $combinations;
    }

    /**
     * @param string $text
     * @param Synonym $synonym
     * @return bool
     */
    private static function hasSynonym(string $text, Synonym $synonym): bool
    {
        for ($length = $synonym->getSpacesCount() + 1; $length >= 1; $length--) {
//            foreach ([$ad->getTitle(), mb_substr($ad->getText(), 0, 200)] as $text) {
                if ($wordsCombinations = self::getWordsCombinations($text, $length)) {
                    foreach ($wordsCombinations as $combination) {
                        if (mb_strtolower($combination) == mb_strtolower($synonym->getTitle())) {
                            return true;
                        }
                    }
                }
//            }
        }

        return false;
    }

    private static function moveAd(Ad $ad, Category $category, string $field, Synonym $synonym): void
    {
        if ($ad->getCategory()->getId() != $category->getId()) {
            Logger::notice('Moved ad '
                . $ad->getId()
                . ' "'
                . $ad->getTitle()
                . ' from "'
                . $ad->getCategoriesTitle()
                . '" to "'
                . implode('/', $category->getWithParentsTitles())
                . '" (found synonym "'
                . $synonym->getTitle()
                . '" in field "'
                . $field . '")'
            );
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
    }

    private static function getWordsCombinations(string $text, int $length): array
    {
        $combinations = [];
        $text = mb_strtolower(Filter::removeEmoji($text));
        $words = array_values(array_filter(explode(' ', strtr($text, [
            '.' => ' ',
            ',' => ' ',
            '!' => ' ',
            '?' => ' ',
            '/' => ' ',
            '\'' => ' ',
            '"' => ' ',
            '*' => ' ',
            ':' => ' ',
            ';' => ' ',
            '-' => ' ',
            '#' => ' ',
            '(' => ' ',
            ')' => ' '
        ]))));
        for ($offset = 0; $offset <= count($words) - $length; $offset++) {
            $combinations[] = trim(implode(' ', array_slice($words, $offset, $length)));
        }

        return $combinations;
    }
}