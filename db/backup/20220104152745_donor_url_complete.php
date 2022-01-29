<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DonorUrlComplete extends AbstractMigration
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
        $category = $this->fetchRow('SELECT * FROM categories LIMIT 1');
        $ad = $this->fetchRow('SELECT * FROM ads LIMIT 1');
        if ($category && $ad) {
            $categoryDonorUrl = new \Palto\Url($category['donor_url']);
            $adUrl = new \Palto\Url($ad['url']);
            if (!$categoryDonorUrl->getDomain() && $adUrl->getDomain()) {
                $this->execute('update categories set donor_url=CONCAT("' . $adUrl->getDomain() . '", donor_url)');
            }
        }
    }
}
