<?php

namespace Palto;

use Pylesos\PylesosService;
use Pylesos\Scheduler;
use Symfony\Component\DomCrawler\Crawler;

abstract class AdsParser
{
    protected const IS_PROXY_REQUIRED = true;

    protected const IS_CRON_DISABLED = false;

    abstract protected function findAds(Crawler $categoryDocument);
    abstract protected function findAdUrl(Crawler $resultRow, Category $category): ?Url;

    protected function getFirstPageNumber(): int
    {
        return 1;
    }

    protected function getNextPageNumber(Crawler $categoryDocument, Category $category, Url $url, int $pageNumber): int
    {
        if (Parser::hasNextPageLinkTag($categoryDocument)) {
            return Parser::getNextPageNumber($categoryDocument);
        }

        return $pageNumber + 1;
    }

    protected function getNextPageUrl(Crawler $categoryDocument, Category $category, Url $url, int $pageNumber): ?Url
    {
        if (Parser::hasNextPageLinkTag($categoryDocument)) {
            return Parser::getNextPageUrl($categoryDocument);
        }

        return null;
    }

    public function run(string $file)
    {
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
                $leafCategories = Categories::getLeafs();
                if ($leafCategories) {
                    shuffle($leafCategories);
                    $leafCategoriesCount = count($leafCategories);
                    foreach ($leafCategories as $leafKey => $category) {
                        $logContent = [
                            'iteration' => ($leafKey + 1) . '/' . $leafCategoriesCount
                        ];
                        Logger::info('Parsing category ' . $category->getTitle(), $logContent);
                        $this->parseCategory($category, $category->getDonorUrl(), $this->getFirstPageNumber(), $logContent);
                    }
                } else {
                    Logger::info('Categories not found');
                }
            },
            function(\Exception $e) {
                Logger::warning($e->getMessage());
            }
        );
        Logger::info('Finished ads parsing with pid=' . $pid);
    }

    private function parseCategory(Category $category, Url $url, int $pageNumber, array $logContent = [])
    {
        $categoryResponse = PylesosService::get($url->getFull(), [], Config::getEnv());
        $categoryDocument = new Crawler($categoryResponse->getResponse());
        $extendedLogContext = array_merge(
            [
                'category' => $category->getTitle(),
                'url' => $url->getFull()
            ],
            $logContent
        );
        $ads = $this->findAds($categoryDocument);
        Logger::info('Found ' . count($ads) . ' ads', $extendedLogContext);
        $addedAdsCount = 0;
        $ads->each(function (Crawler $resultRow, $i) use (&$addedAdsCount, $category) {
            $adUrl = $this->findAdUrl($resultRow, $category);
            if (!$adUrl) {
                Logger::error('Url not parsed: ' . $resultRow->outerHtml());
            } elseif (!Ads::getByUrl($adUrl)) {
                $adId = Parser::safeTransaction(function () use ($category, $adUrl) {
                    $adResponse = PylesosService::get($adUrl, [], Config::getEnv());
                    $adDocument = new Crawler($adResponse->getResponse());

                    return $this->parseAd($adDocument, $category, $adUrl);
                });

                if ($adId) {
                    Logger::debug('Added ad with id=' . $adUrl);
                    $addedAdsCount++;
                } else {
                    Logger::debug('Skipped wrong ad with url ' . $adUrl->getFull());
                }

            } else {
                Logger::debug('Ad with url ' . $adUrl . ' already exists');
            }
        });
        Logger::info('Added ' . $addedAdsCount . ' ads from page ' . $url, $extendedLogContext);
        $nextPageNumber = $this->getNextPageNumber($categoryDocument, $category, $url, $pageNumber);
        if ($nextPageNumber && $nextPageNumber <= 10) {
            $nextUrl = $this->getNextPageUrl($categoryDocument, $category, $url, $pageNumber);
            Logger::debug('Parsing next page ' . $nextUrl);
            $this->parseCategory($category, $nextUrl, $nextPageNumber + 1, $logContent);
        }
    }
}