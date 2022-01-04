<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CraigslistLayoutsChanged extends AbstractMigration
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
            'layouts/list.php' => [
                '\'title\' => implode(... . \'' => '\'title\' => $this->generateHtmlTitle() ',
                '$this->getCategories($this->getCategory())' => '$this->getWithAdsCategories($this->getCategory())',
                '$this->getCategories(0, 1)' => '$this->getWithAdsCategories()'
            ],
            'layouts/ad.php' => [
                '\'title\' => ...
    \'description\'' => '\'title\' => $this->generateHtmlTitle(),',
                'getPrice() > )' => 'getPrice() > 0)',
                '🏷 <?=$this->getAd()->getCurrency()?><?=number_format($this->getAd()->getPrice())?></span>' => '🏷 <?=$this->getAd()->getCurrency()?><?=number_format($this->getAd()->getPrice())?>'
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
