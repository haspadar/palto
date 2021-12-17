<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateRegistrationUrl extends AbstractMigration
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
        $layouts = [];
        foreach (scandir('layouts') as $file) {
            if (!in_array($file, ['.', '..'])) {
                if (is_dir('layouts/' . $file)) {
                    foreach (scandir('layouts/' . $file) as $partialFile) {
                        if (!in_array($partialFile, ['.', '..'])) {
                            $layouts[] = 'layouts/' . $file . '/' . $partialFile;
                        }
                    }
                } else {
                    $layouts[] = 'layouts/' . $file;
                }
            }
        }

        foreach ($layouts as $layout) {
            $layoutContent = file_get_contents($layout);
            if (mb_strpos($layout, '/all/registration') !== false) {
                $layoutContent = str_replace('/all/registration', '/registration', $layoutContent);
                file_put_contents($layout, $layoutContent);
                echo 'Updated registration url in ' . $layout . PHP_EOL;
            }
        }
    }
}
