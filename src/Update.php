<?php

namespace Palto;

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Update
{
    /**
     * @var mixed
     */
    private string $databaseName;
    /**
     * @var mixed
     */
    private string $databasePassword;
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger('update');
        $handler = new StreamHandler('php://stdout');
        $handler->setFormatter(new ColoredLineFormatter());
        $this->logger->pushHandler($handler);
        $databaseCredentials = Palto::extractDatabaseCredentials(
            file_get_contents(Directory::getRootDirectory() . '/.env')
        );
        $this->databaseName = $databaseCredentials['DB_NAME'];
        $this->databasePassword = $databaseCredentials['DB_PASSWORD'];
    }

    public function replaceCode(array $replaces)
    {
        foreach ($replaces as $file => $fileReplaces) {
            $content = file_get_contents(Directory::getRootDirectory() . '/' . $file);
            foreach ($fileReplaces as $from => $to) {
                $isReplaceBeforeSemicolon = mb_substr($from, -3) == '...';
                if ($isReplaceBeforeSemicolon) {
                    $start = mb_strpos($content, mb_substr($from, 0, -3));
                    $finish = mb_strpos($content, ';', $start);
                    if ($start !== false && $finish !== false) {
                        $content = mb_substr($content, 0, $start)
                            . $to
                            . mb_substr($content, $finish + 1);
                    }

                } else {
                    $content = str_replace($from, $to, $content);
                }
            }

            file_put_contents(Directory::getRootDirectory() . '/' . $file, $content);
            $this->logger->info('Replaced ' . $file);
        }
    }

    private function getReplaceBeforeSemicolon(string $file, string $from)
    {
        $content = file_get_contents(Directory::getRootDirectory() . '/' . $file);
        $start = mb_strpos($content, $from);
        $finish = mb_strpos($content, ';', $start);
    }
}