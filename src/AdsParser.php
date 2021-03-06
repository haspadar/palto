<?php

namespace Palto;

use Pylesos\PylesosService;
use Pylesos\Scheduler;
use Symfony\Component\DomCrawler\Crawler;

abstract class AdsParser
{
    protected const IS_PROXY_REQUIRED = true;

    protected const IS_CRON_DISABLED = false;

    protected const MAX_PAGE_NUMBER = 10;

    abstract protected function findAds(Crawler $leafDocument);

    abstract protected function findAdUrl(Crawler $resultRow, Category|Region $category): ?Url;

    protected function getFirstPageNumber(): int
    {
        return 1;
    }

    protected function getNextPageNumber(Crawler $leafDocument, Category|Region $leaf, Url $url, int $pageNumber): int
    {
        if (Parser::hasNextPageLinkTag($leafDocument)) {
            Logger::debug('hasNextPageLinkTag: true');

            return Parser::getNextPageNumber($leafDocument);
        }

        return $pageNumber + 1;
    }

    protected function getNextPageUrl(Crawler $leafDocument, Category|Region $leaf, Url $url, int $pageNumber): ?Url
    {
        if (Parser::hasNextPageLinkTag($leafDocument)) {
            return Parser::getNextPageUrl($leafDocument);
        }

        return null;
    }

    protected function getTreeLeafs(): array
    {
        return Categories::getLeafs();
    }

    public function run(string $file)
    {
        Logger::info('Ads parser started');
        if (self::IS_CRON_DISABLED && Cli::isCron()) {
            Logger::error('Cron parsing disabled');

            exit;
        } elseif (self::IS_CRON_DISABLED && Cli::isCli()) {
            Logger::warning('Cron parsing disabled');
        }

        if (static::IS_PROXY_REQUIRED && !Config::get('ROTATOR_URL')) {
            Logger::error('Env option ROTATOR_URL is empty');

            return;
        }

        $fileParts = explode('/', $file);
        $fileShortName = $fileParts[count($fileParts) - 1];
        $pid = Status::getPhpCommandPid($fileShortName, Directory::getProjectName());
        Logger::info('Started ads parsing with pid=' . $pid);
        $scheduler = new Scheduler(Config::getEnv());
        $scheduler->run(
            function () use ($pid) {
                $leafs = $this->getTreeLeafs();
                if ($leafs) {
                    shuffle($leafs);
                    $leafsCount = count($leafs);
                    foreach ($leafs as $leafKey => $leaf) {
                        $logContent = [
                            'iteration' => ($leafKey + 1) . '/' . $leafsCount
                        ];
                        Logger::info('Parsing leaf ' . $leaf->getTitle(), $logContent);
                        $this->parseLeaf($leaf, $leaf->getDonorUrl(), $this->getFirstPageNumber(), $logContent);
                    }
                } else {
                    Logger::info('Leafs not found');
                }
            },
            function(\Exception $e) {
                Logger::warning($e->getMessage());
            }
        );
        Logger::info('Finished ads parsing with pid=' . $pid);
    }

    private function parseLeaf(Category|Region $leaf, Url $url, int $pageNumber, array $logContent = [])
    {
        $leafResponse = PylesosService::get($url->getFull(), [], Config::getEnv());
        $leafDocument = new Crawler($leafResponse->getResponse());
        $extendedLogContext = array_merge(
            [
                'leaf' => $leaf->getTitle(),
                'url' => $url->getFull()
            ],
            $logContent
        );
        $ads = $this->findAds($leafDocument);
        Logger::info('Found ' . count($ads) . ' ads', $extendedLogContext);
        $addedAdsCount = 0;
        $ads->each(function (Crawler $resultRow, $i) use (&$addedAdsCount, $leaf, $pageNumber) {
            $adUrl = $this->findAdUrl($resultRow, $leaf);
            if (!$adUrl) {
                Logger::error('Url not parsed: ' . $resultRow->outerHtml());
            } elseif (!Ads::getByUrl($adUrl)) {
                $adId = Parser::safeTransaction(function () use ($leaf, $adUrl) {
                    $adResponse = PylesosService::get($adUrl, [], Config::getEnv());
                    if ($adResponse->getResponse()) {
                        $adDocument = new Crawler($adResponse->getResponse());

                        return $this->parseAd($adDocument, $leaf, $adUrl);
                    }

                    return 0;
                });
                if ($adId) {
                    $adNumber = $i + 1;
                    Logger::debug("Added {$adNumber}th ad on page $pageNumber with id=$adUrl");
                    $addedAdsCount++;
                } else {
                    Logger::debug('Skipped wrong ad with url ' . $adUrl->getFull());
                }

            } else {
                Logger::debug('Ad with url ' . $adUrl . ' already exists');
            }
        });
        Logger::info('Added ' . $addedAdsCount . ' ads from page ' . $url, $extendedLogContext);
        $nextPageNumber = $this->getNextPageNumber($leafDocument, $leaf, $url, $pageNumber);
        if ($nextPageNumber && $nextPageNumber <= static::MAX_PAGE_NUMBER) {
            $nextUrl = $this->getNextPageUrl($leafDocument, $leaf, $url, $pageNumber);
            if ($nextUrl) {
                Logger::debug('Parsing next page ' . $nextUrl);
                $this->parseLeaf($leaf, $nextUrl, $nextPageNumber + 1, $logContent);
            } else {
                Logger::warning('Not found next page on url ' . $url);
            }

        } else {
            Logger::warning('Ignored next page number ' . $nextPageNumber);
        }
    }
}