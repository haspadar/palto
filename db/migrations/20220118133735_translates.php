<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Palto\Cli;
use Palto\Directory;
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
        \Palto\Cli::runCommands([
            'mv ' . \Palto\Directory::getConfigsDirectory() . \Palto\Directory::getStructureDirectory() . '/',
            'mv ' . \Palto\Directory::getRootDirectory() . '/.env ' . \Palto\Directory::getConfigsDirectory(),
            'Copy Pylesos env' => \Palto\Cli::safeCopyPylesosEnv()
        ]);
        \Palto\Update::run();
        $this->reGenerateEnv(
            Directory::getStructureConfigsDirectory(),
            '.pylesos',
            Directory::getConfigsDirectory(),
            Dotenv::createUnsafeMutable(Directory::getConfigsDirectory(), '.env')->load()
        );
        $this->reGenerateEnv(
            Directory::getStructureConfigsDirectory(),
            '.env',
            Directory::getConfigsDirectory(),
            Dotenv::createUnsafeMutable(Directory::getConfigsDirectory(), '.env')->load()
        );
    }

    private function reGenerateEnv(string $structureConfigDirectory, string $envFileName, string $configDirectoryDirectory, array $fullEnv)
    {
        $replaces = [];
        $structureEnv = Dotenv::createUnsafeMutable($structureConfigDirectory, $envFileName)->load();
        foreach ($fullEnv as $key => $value) {
            if (isset($structureEnv[$key]) && strlen($value)) {
                $replaces[$key . '="' . $structureEnv[$key] . '"'] = $key . '="' . $value . '"';
                $replaces[$key . '=' . $structureEnv[$key]] = $key . '=' . $value;
            }
        }

        $replaces['SUNDUK_URL=""'] = 'SUNDUK_URL="http://95.216.222.193/"';
        $replaces['YANDEX_TRANSLATE_API_KEY='] = 'YANDEX_TRANSLATE_API_KEY="AQVNwmJ4oPONRgtb76ZwHk8tVrYWSmkMyD1vEMNu"';
        $replaces['ROTATOR_URL=""'] = 'ROTATOR_URL="https://rotator.dev/list.php?key=Rq9S-3SY3QC6"';
        $content = strtr(file_get_contents($structureConfigDirectory . '/' . $envFileName), $replaces);
        \Palto\Debug::dump($content, $configDirectoryDirectory . '/' . $envFileName);
        file_put_contents($configDirectoryDirectory . '/' . $envFileName, $content);
    }
}