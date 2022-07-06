<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TranslatesFromFile extends AbstractMigration
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
        $this->execute("RENAME TABLE `uspets`.`translates` TO `yandex_translates`;");
        $this->execute("CREATE TABLE `translates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(600) NOT NULL DEFAULT '',
  `value` text NULL
);");
        $this->execute("ALTER TABLE `translates` ADD UNIQUE `name` (`name`);");

        $translates = require_once \Palto\Directory::getConfigsDirectory() . '/translates.php';
        foreach ($translates as $name => $value) {
            $this->execute("INSERT INTO `translates` (`name`, `value`) VALUES ('$name', '" . strtr($value, ['\'' => '\\\'', '"' => '\\"']) . "');");
        }
// Run mole RemoveEnvConstants.php for:
// KARMAN_PANEL (.env)
// SUNDUK_URL (.env)
// SMTP_EMAIL (.env)
// SMTP_PASSWORD (.env)
// SMTP_FROM (.env)
// SMTP_HOST (.env)
// SMTP_PORT (.env)
// SMTP_ENCRYPTION (.env)
// YANDEX_TRANSLATE_API_KEY (.env)
// PARSE_ADS_SCRIPT (.env)
// PARSE_CATEGORIES_SCRIPT (.env)
// AUTH(.env)
// AUTH_LOGIN (.env)
// AUTH_PASSWORD (.env)

// LAYOUT_THEME (.layouts)
// DEFAULT_REGION_TITLE (.layouts)
// DEFAULT_REGION_URL (.layouts)
// DONOR_URL (.layouts)
// YOUTUBE_URL (.layouts)
//HOT_LAYOUT (.layouts)
//HOT_LAYOUT_REGIONS (.layouts)
//HOT_LAYOUT_CATEGORIES_LEVEL_1 (.layouts)
//HOT_LAYOUT_CATEGORIES_LEVEL_2 (.layouts)
//HOT_LAYOUT_HOT_CATEGORY (.layouts)
//HOT_LAYOUT_HOT_ADS (.layouts)
//HOT_LAYOUT_NEW_ADS (.layouts)

//        Удалить файл counter.php
//        Удалить файл translates.english.php
//        Удалить файл translates.russian.php
//        Удалить файл translates.php

//        Вернуть в index.php Auth::check()

    }
}
