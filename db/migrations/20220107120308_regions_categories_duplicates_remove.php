<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RegionsCategoriesDuplicatesRemove extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("DELETE r1 FROM regions r1 INNER JOIN regions r2 WHERE r1.id < r2.id AND r1.donor_url = r2.donor_url;");
        $this->execute("DELETE c1 FROM categories r1 INNER JOIN categories c2 WHERE c1.id < c2.id AND c1.donor_url = c2.donor_url;");
    }
}
