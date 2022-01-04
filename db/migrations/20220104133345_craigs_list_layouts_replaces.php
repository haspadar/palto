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
            'layouts/index.php' => [
                '$this->getRegions(0, 1)' => '$this->getWithAdsRegions()',
                '$this->getRegions($level1Region->getId())' => '$this->getWithAdsRegions($level1Region)',
                'getCategories(0, 1)' => 'getWithAdsCategories()',
                'getCategories($level1Category->getId())' => 'getWithAdsCategories($level1Category)'
            ],
            'layouts/categories-list.php' => [
                '$this->getWithAdsCategories(0, 1)' => '$this->getWithAdsCategories()',
                '$this \Palto\Palto' => '$this \Palto\Layout',
                'getWithAdsCategories($level2Category[\'id\'])' => 'getWithAdsCategories($level2Category)',
                'getWithAdsCategories($level1Category[\'id\'])' => 'getWithAdsCategories($level1Category)',
                'Category[\'id\']' => 'Category->getId()',
                'Category[\'title\']' => 'Category->getTitle()',
            ],
            'layouts/regions-list.php' => [
                '$this->getRegions(0, 1)' => '$this->getWithAdsRegions()',
                '$this->getRegions($level1Region[\'id\'])' => '$this->getRegions($level1Region)',
                'Region[\'title\']' => 'Region->getTitle()'
            ]
        ];
        $rootDirectory = realpath('.');
        while (!file_exists($rootDirectory . '/.env')) {
            $rootDirectory = realpath('..');
        }

        \Palto\Directory::setRootDirectory($rootDirectory);
        \Palto\Update::replaceCode($replaces);
        exit;
    }
}
