<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Translates extends AbstractMigration
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
//        \Palto\Cli::runCommands([
//            'mv ' . \Palto\Directory::getConfigsDirectory() . \Palto\Directory::getStructureDirectory() . '/',
//            'mv ' . \Palto\Directory::getRootDirectory() . '/.env ' . \Palto\Directory::getConfigsDirectory(),
//        ]);
//        \Palto\Update::run();

        \Palto\Debug::dump(self::extractLayoutTranslate('<html lang="', 0, '"', 'client/partials/header.inc'));
        exit;
        $extractedTranslates = $this->extractTranslates();
        $yandexTranslates = \Palto\Translates::getYandexTranslates();
        $extractedTranslates = $this->extractTranslates();
        \Palto\Translates::setTranslates($extractedTranslates);

        $extractedCounters = $this->extractCounters();
        \Palto\Counters::setCounters($extractedCounters);


//        $replaces = [
//            'layouts/*' => [
//                '\Palto\Layout' => '\Palto\Layout\Client'
//            ]
//        ];
//        \Palto\Update::replaceCode($replaces);
    }

    private function extractTranslates()
    {
        return [
            'client/partials/header.inc' => [
                'logo_alt' => ['<img src="/img/logo.png" alt="', 0, '"'],
                'html_lang' => ['<html lang="', 0, '"'],
            ],
            'client/partials/footer.inc' => [
                'Частные бесплатные объявления в %s' => ['class="footer">', 0, '</a>'],
                'Агрегатор всех местных досок объявлений' => ['</a> - ', 0, ' | <a href="'],
                'Контакты' => ['class="footer">', 1, ': '],
                'Текст про куки' => ['cookie_notification">
    <div>', 0, '</div>', 'This website uses cookies to personalise content and ads, to provide social media features and to analyse our traffic. We also share information about your use of our site with our social media, advertising and analytics partners who may combine it with other information that you’ve provided to them or that they’ve collected from your use of their services.'],
                'СОГЛАСЕН' => ['cookie_accept">', 0, '</button>', 'ACCEPT'],
                'Следующая' => ''
            ],
            'client/partials/pager.inc' => [
                'Предыдущая' => ['previousPageUrl\')?>">« ', 0, '</a>'],
                'Следующая' => ['nextPageUrl\')?>"> ', 0, ' »</a>']
            ],
            'client/404.php' => [
                'Объявление было удалено' => ['<h1>', 0, '</h1>'],
                'Не найдено' => ['<h1>', 1, '</h1>'],
            ],
            'client/static/categories-list.php'
        ];
    }

    private function extractLayoutTranslate(string $after, int $keyNumber, string $before, string $layout): string
    {
        $content = file_get_contents(\Palto\Directory::getLayoutsDirectory() . '/' . $layout);
        $afterPosition = mb_strpos($content, $after);
        if ($keyNumber == 1) {
            $afterPosition = mb_strpos($content, $after, $afterPosition);
        }

        $beforePosition = mb_strpos($content, $before, $afterPosition);

        return mb_substr($content, $afterPosition + mb_strlen($afterPosition), $beforePosition - $afterPosition - mb_strlen($afterPosition));
    }
}
