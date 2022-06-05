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

    /**
     * @param Ad $ad
     * @param Synonym[] $synonyms
     * @return array
     */
    public static function find(Ad $ad, array $synonyms): array
    {
        $level2Synonyms = array_filter($synonyms, fn(Synonym $synonym) => $synonym->getCategory()->getLevel() == 2);
        $level1Synonyms = array_filter($synonyms, fn(Synonym $synonym) => $synonym->getCategory()->getLevel() == 1);
        Debug::dump(array_map(fn(Synonym $synonym) => $synonym->getTitle(), $synonyms), '$level2Synonyms');
        foreach ([$level2Synonyms, $level1Synonyms] as $levelSynonyms) {
            foreach ([$ad->getTitle(), $ad->getText(200)] as $key => $text) {
                foreach ($levelSynonyms as $synonym) {
                    if (self::hasSynonym($text, $synonym)) {
                        return [
                            'category' => $synonym->getCategory(),
                            'field' => $key ? 'text' : 'title',
                            'synonym' => $synonym
                        ];
                    }
                }
            }
        }

        return ['category' => Categories::createUndefined(), 'field' => '', 'synonym' => null];
    }

    /**
     * @param Category[] $categories
     * @return int
     */
    public static function findAndMoveAds(array $categories = []): int
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $synonyms = Synonyms::getAll();
        $movedAdsCount = $synonyms ? self::moveCategoryAds($synonyms, $categories) : 0;
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
        $limit = 1000;
        $offset = 0;
        $adsCount = Ads::getAdsCount(array_map(fn(Category $category)=> $category->getId(), $categories));
        $movedAdsCount = 0;
        while ($ads = Ads::getFields($categories, ['id', 'title', 'text', 'category_id', 'region_id', 'deleted_time'], $limit, $offset)) {
            foreach ($ads as $key => $adFields) {
                $ad = new Ad($adFields, [], []);
                $found = self::find($ad, $synonyms);
                $progress = $offset + count($ads) + $key . '/' . $adsCount;
//                if ($found['synonym'] && $ad->getCategory()->getId() != $found['synonym']->getCategory()->getId()) {
                if ($found['synonym']) {
                    self::moveAd($ad, $found['field'], $found['synonym'], $progress);
                    $movedAdsCount++;
                } else {
                    Logger::debug('Skipped ad ' . $progress);
                }
            }

            $offset += $limit;
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
        if ($combinations = self::getCombinations($text, $synonym->getWordsCount())) {
            foreach ($combinations as $combination) {
                if (mb_strtolower($combination) == mb_strtolower($synonym->getTitle())) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function moveAd(Ad $ad, string $field, Synonym $synonym, $progress): void
    {
        Logger::notice('Moved ad '
            . $ad->getId()
            . ' "'
            . $ad->getTitle()
            . '" from "'
            . $ad->getCategoryPath()
            . '" to "'
            . $synonym->getCategory()->getPath()
            . '" (found synonym "'
            . $synonym->getTitle()
            . '" in field "'
            . $field . '"), '
            . $progress
        );
        Ads::update([
            'category_id' => $synonym->getCategory()->getId(),
            'category_level_1_id' => $synonym->getCategory()->getLevel() == 1
                ? $synonym->getCategory()->getId()
                : $synonym->getCategory()->getParentId(),
            'category_level_2_id' => $synonym->getCategory()->getLevel() == 2
                ? $synonym->getCategory()->getId()
                : null,
            'synonym_id' => $synonym->getId(),
            'field' => $field
        ], $ad->getId());
    }

    private static function getCombinations(string $text, int $combinationsWordsLimit): array
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
        if ($combinationsWordsLimit > count($words)) {
            $combinationsWordsLimit = count($words);
        }

        for ($lengthIterator = $combinationsWordsLimit; $lengthIterator >= 1; $lengthIterator--) {
            for ($offset = 0; $offset <= count($words) - $lengthIterator; $offset++) {
                $combinationWords = array_slice($words, $offset, $lengthIterator);
                $combination = trim(implode(' ', $combinationWords));
                if (count($combinationWords) == $lengthIterator && !in_array($combination, $combinations)) {
                    $combinations[] = $combination;
                }
            }
        }

        return $combinations;
    }

    public static function getById(?int $id): ?Synonym
    {
        return $id ? new Synonym((new \Palto\Model\Synonyms())->getById($id)) : null;
    }
}