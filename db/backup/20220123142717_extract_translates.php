<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ExtractTranslates extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $extractedTranslates = \Palto\Translates::extractTranslates();
        $isRussian = mb_substr($extractedTranslates['html_lang'], 0, 2) == 'ru';
        $fileName = \Palto\Directory::getConfigsDirectory() . ($isRussian ? '/translates.russian.php' : '/translates.english.php');
        $defaultTranslates = require_once $fileName;
        \Palto\Translates::saveTranslates($extractedTranslates, $defaultTranslates, $fileName);

        $extractedCounters = \Palto\Counters::extractCounters();
        \Palto\Counters::saveCounters($extractedCounters, \Palto\Directory::getConfigsDirectory() . '/counters.php');

    }
}
