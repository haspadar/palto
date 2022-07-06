<?php
declare(strict_types=1);

use Palto\Directory;
use Phinx\Migration\AbstractMigration;

final class RemoveConfigs extends AbstractMigration
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
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('sunduk_url', 'Ссылка для бэкапов', '" . \Palto\Config::get('SUNDUK_URL') . "', 'Реквизиты', 'url');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('smtp_email', 'Исходящая почта', '" . \Palto\Config::get('SMTP_EMAIL') . "', 'Реквизиты', 'email');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('smtp_password', 'Пароль для исходящей почты', '" . \Palto\Config::get('SMTP_PASSWORD') . "', 'Реквизиты', 'password');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('smtp_from', 'Имя отправителя для исходящей почты', '" . \Palto\Config::get('SMTP_FROM') . "', 'Реквизиты', 'string');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('smtp_host', 'Хост для исходящей почты', '" . \Palto\Config::get('SMTP_HOST') . "', 'Реквизиты', 'host');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('smtp_port', 'Порт для исходящей почты', '" . \Palto\Config::get('SMTP_PORT') . "', 'Реквизиты', 'int');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('smtp_encryption', 'Шифрование для исходящей почты', '" . \Palto\Config::get('SMTP_ENCRYPTION') . "', 'Реквизиты', 'string');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('yandex_translate_api_key', 'Ключ для Yandex Translate API', '" . \Palto\Config::get('YANDEX_TRANSLATE_API_KEY') . "', 'Реквизиты', 'string');");

        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('is_auth_enabled', 'Аутентификация включена', '" . \Palto\Config::get('AUTH') . "', 'Реквизиты', 'bool');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('auth_login', 'Логин для аутентификации', '" . \Palto\Config::get('AUTH_LOGIN') . "', 'Реквизиты', 'string');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('auth_password', 'Пароль для аутентификации', '" . \Palto\Config::get('AUTH_PASSWORD') . "', 'Реквизиты', 'password');");
        $this->execute("UPDATE settings SET value='" . \Palto\Config::get('LAYOUT_THEME') . "' WHERE name='template_theme'");

        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('default_region_title', 'Название региона по умолчанию', '" . \Palto\Config::get('DEFAULT_REGION_TITLE') . "', 'Шаблоны', 'string');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('default_region_url', 'Урл региона по умолчанию', '" . \Palto\Config::get('DEFAULT_REGION_URL') . "', 'Шаблоны', 'string');");

        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('is_donor_url_enabled', 'Показывать ссылку на донора', '" . \Palto\Config::get('DONOR_URL') . "', 'Шаблоны', 'bool');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('is_youtube_url_enabled', 'Показывать YouTube-ролик', '" . \Palto\Config::get('YOUTUBE_URL') . "', 'Шаблоны', 'bool');");

        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('is_hot_template_enabled', 'Показывать шаблон Hot', '" . \Palto\Config::get('HOT_LAYOUT') . "', 'Шаблоны', 'bool');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('hot_layout_regions', 'Количество регионов в шаблоне Hot', '" . \Palto\Config::get('HOT_LAYOUT_REGIONS') . "', 'Шаблоны', 'int');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('hot_layout_categories_level_1', 'Количество категорий 1-го уровня в шаблоне Hot', '" . \Palto\Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_1') . "', 'Шаблоны', 'int');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('hot_layout_categories_level_2', 'Количество категорий 2-го уровня в шаблоне Hot', '" . \Palto\Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2') . "', 'Шаблоны', 'int');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('hot_layout_hot_category', 'Id категории для шаблона Hot', '" . \Palto\Config::get('HOT_LAYOUT_HOT_CATEGORY') . "', 'Шаблоны', 'int');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('hot_layout_hot_ads', 'Количество горячих объявлений в шаблоне Hot', '" . \Palto\Config::get('HOT_LAYOUT_HOT_ADS') . "', 'Шаблоны', 'int');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('hot_layout_new_ads', 'Количество новых объявлений в шаблоне Hot', '" . \Palto\Config::get('HOT_LAYOUT_NEW_ADS') . "', 'Шаблоны', 'int');");

        $counters = require_once Directory::getConfigsDirectory() . '/counters.php';
        foreach ($counters as &$counter) {
            if (is_array($counter)) {
                foreach ($counter as &$value) {
                    $value = strtr($value, ['\'' => '\\\'', '"' => '\\"']);
                }
            } else {
                $counter = strtr($counter, ['\'' => '\\\'', '"' => '\\"']);
            }
        }

        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('liveinternet', 'Счётчик liveinternet', '" . ($counters['liveinternet'] ?? '') . "', 'Коды', 'code');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('google_header', 'Счётчик google для хедера', '" . ($counters['google_header'] ?? '') . "', 'Коды', 'code');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('google', 'Счётчик google', '" . ($counters['google'] ?? '') . "', 'Коды', 'code');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('adx', 'Счётчики AdExchange', '" . implode(PHP_EOL, array_map(fn($counter) => '', $counters['adx'] ?? [])) . "', 'Коды', 'codes');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('google_auto', 'Счётчик google_auto', '" . ($counters['google_auto'] ?? '') . "', 'Коды', 'code');");
        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('google_search', 'Поисковая строка', '" . ($counters['google_search'] ?? '') . "', 'Коды', 'code');");
    }
}
