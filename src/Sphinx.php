<?php
namespace Palto;

class Sphinx
{
    const UPDATE_SPHINX_CONFIG_FILE = 'update_sphinx_config.php';
    const SPHINX_GLOBAL_DIRECTORY = '/var/www/sphinx/';
    const SPHINX_GLOBAL_CONFIG = self::SPHINX_GLOBAL_DIRECTORY . 'sphinx.conf';
    const SPHINX_LOCAL_CONFIG = 'sphinx_source_index.conf';
    public const REINDEX_SCRIPT = 'reindex.php';

    public function addConfig(string $localConfigsPath, string $databaseName, string $databasePassword)
    {
        $localConfig = strtr(
            file_get_contents($localConfigsPath),
            [
                '[DB_USER]' => $databaseName,
                '[DB_PASSWORD]' => $databasePassword,
                '[DB_NAME]' => $databaseName,
                '[PROJECT]' => $databaseName
            ]
        );
        $this->saveGlobalConfig(str_replace(
            'indexer',
            $localConfig . PHP_EOL . 'indexer',
            self::SPHINX_GLOBAL_CONFIG
        ));
    }

    public function reIndex(): string
    {
        return system("indexer --all --rotate");
    }

    public function install(string $projectsPath = self::SPHINX_GLOBAL_DIRECTORY): void
    {
        $paltoSphinxDirectory = __DIR__ . '/../sphinx';
        $copyCommand = "cp -R -n $paltoSphinxDirectory " . $projectsPath;
//        `$copyCommand`;
        $localConfigPattern = file_get_contents(__DIR__ . '/../configs/sphinx_source_index.conf');
        foreach ($this->getPaltoProjects($projectsPath) as $projectName) {
            echo 'Found palto project ' . $projectName . PHP_EOL;
            $response = file_get_contents($projectsPath . $projectName . '/.env');
            $databaseCredentials = Palto::extractDatabaseCredentials($response);
            $indexName = str_replace('.', '_', $projectName);
            $localConfig = strtr($localConfigPattern, [
                '[DB_USER]' => $databaseCredentials['DB_USER'],
                '[DB_PASSWORD]' => $databaseCredentials['DB_PASSWORD'],
                '[DB_NAME]' => $databaseCredentials['DB_NAME'],
                '[PROJECT]' => $indexName
            ]);
            $globalSphinxConfig = str_replace(
                'indexer',
                $localConfig . PHP_EOL . 'indexer',
                file_get_contents($paltoSphinxDirectory . '/sphinx.conf')
            );
            $this->saveGlobalConfig($globalSphinxConfig);
            echo 'Added sphinx config for ' . $projectName . PHP_EOL;
        }
    }

    private function saveGlobalConfig(string $globalConfig)
    {
        file_put_contents(
            self::SPHINX_GLOBAL_CONFIG,
            $globalConfig
        );
    }

    private function getPaltoProjects(string $projectsPath): array
    {
        $projects = [];
        foreach (scandir($projectsPath) as $file) {
            if (is_dir($projectsPath . $file)
                && mb_substr($file, 0, 1) != '.'
                && file_exists($projectsPath . $file . '/composer.lock')
                && Palto::hasComposerLockPalto(file_get_contents($projectsPath . $file . '/composer.lock'))
            ) {
                $projects[] = $file;
            }
        }

        return $projects;
    }
}