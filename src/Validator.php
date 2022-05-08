<?php

namespace Palto;

class Validator
{
    public static function validateMoveAd(
        int $adId,
        int $categoryLevel1Id,
        string $newCategoryLevel1Title,
        string $newCategoryLevel2Title
    ): string {
        $error = '';
        if ($newCategoryLevel1Title && Categories::getByTitle($newCategoryLevel1Title)) {
            $error = 'Такая категория уже есть';
        } elseif ($newCategoryLevel2Title && Categories::getByTitle($newCategoryLevel2Title, $categoryLevel1Id)) {
            $error = 'Такая категория уже есть';
        }

        return $error;
    }

    public static function validateSynonyms(array $synonyms): string
    {
        foreach ($synonyms as $synonym) {
            if ($found = Synonym::getByTitle($synonym)) {
                return 'Синоним ' . $synonym . ' уже есть у категории ' . Categories::getById($found['category_id'])->getTitle();
            }
        }

        return '';
    }
}