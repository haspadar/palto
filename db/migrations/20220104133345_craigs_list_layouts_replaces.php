<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CraigsListLayoutsReplaces extends AbstractMigration
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
        \Palto\Backup::createArchive();
        $replaces = [
            'layouts/*' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',

                '$this->getRegions(0, 1)' => '$this->getWithAdsRegions()',
                '$this->getRegions($level1Region->getId())' => '$this->getWithAdsRegions($level1Region)',
                '$this->getRegions($level1Region)' => '$this->getWithAdsRegions($level1Region)',
                '$this->getRegions($level1Region[\'id\'])' => '$this->getWithAdsRegions($level1Region)',

                'getCategories(0, 1)' => 'getWithAdsCategories()',
                '$this->getCategories($level1Category->getId())' => '$this->getWithAdsCategories($level1Category)',
                '$this->getCategories($level2Category->getId())' => '$this->getWithAdsCategories($level2Category)',
                '$this->getWithAdsCategories(0, 1)' => '$this->getWithAdsCategories()',
                'getWithAdsCategories($level1Category[\'id\'])' => 'getWithAdsCategories($level1Category)',
                'getWithAdsCategories($level2Category[\'id\'])' => 'getWithAdsCategories($level2Category)',

                'Category[\'id\']' => 'Category->getId()',
                'Category[\'title\']' => 'Category->getTitle()',

                'Region[\'title\']' => 'Region->getTitle()',
                'Region[\'id\']' => 'Region->getId()',
            ]
        ];

        $rootDirectory = realpath('.');
        while (!file_exists($rootDirectory . '/.env')) {
            $rootDirectory = realpath('..');
        }

        \Palto\Directory::setRootDirectory($rootDirectory);
        \Palto\Update::replaceCode($replaces);
    }
}
