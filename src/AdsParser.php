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
    abstract protected function findAdUrl(Crawler $resultRow, CategoryCandidate $candidate): ?Url;

    protected function getFirstPageNumber(): int
    {
        return 1;
    }

    protected function getNextPageNumber(Crawler $categoryDocument, CategoryCandidate $categoryCandidate, Url $url, int $pageNumber): int
    {
        if (Parser::hasNextPageLinkTag($categoryDocument)) {
            Logger::debug('hasNextPageLinkTag: true');

            return Parser::getNextPageNumber($categoryDocument);
        }

        return $pageNumber + 1;
    }

    protected function getNextPageUrl(Crawler $categoryDocument, CategoryCandidate $categoryCandidate, Url $url, int $pageNumber): ?Url
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
                $leafCategoriesCandidates = CategoriesCandidates::getLeafs();
                if ($leafCategoriesCandidates) {
                    shuffle($leafCategoriesCandidates);
                    $leafCategoriesCount = count($leafCategoriesCandidates);
                    foreach ($leafCategoriesCandidates as $leafKey => $categoryCandidate) {
                        $logContent = [
                            'iteration' => ($leafKey + 1) . '/' . $leafCategoriesCount
                        ];
                        Logger::info('Parsing category candidate ' . $categoryCandidate->getTitle(), $logContent);
                        $this->parseCategory($categoryCandidate, $categoryCandidate->getDonorUrl(), $this->getFirstPageNumber(), $logContent);
                    }
                } else {
                    Logger::info('Categories candidates not found');
                }
            },
            function(\Exception $e) {
                Logger::warning($e->getMessage());
            }
        );
        Logger::info('Finished ads parsing with pid=' . $pid);
    }

    abstract protected function parseAd(Crawler $adDocument, CategoryCandidate $categoryCandidate, Url $adUrl): int;

    private function parseCategory(CategoryCandidate $categoryCandidate, Url $url, int $pageNumber, array $logContent = [])
    {
        $categoryResponse = PylesosService::get($url->getFull(), [], Config::getEnv());
        $categoryDocument = new Crawler($categoryResponse->getResponse());
        $extendedLogContext = array_merge(
            [
                'category_candidate' => $categoryCandidate->getTitle(),
                'url' => $url->getFull()
            ],
            $logContent
        );
        $ads = $this->findAds($categoryDocument);
        Logger::info('Found ' . count($ads) . ' ads', $extendedLogContext);
        $addedAdsCount = 0;
        $ads->each(function (Crawler $resultRow, $i) use (&$addedAdsCount, $categoryCandidate, $pageNumber) {
            $adUrl = $this->findAdUrl($resultRow, $categoryCandidate);
            if (!$adUrl) {
                Logger::error('Url not parsed: ' . $resultRow->outerHtml());
            } elseif (!Ads::getByUrl($adUrl)) {
                $adId = Parser::safeTransaction(function () use ($categoryCandidate, $adUrl) {
                    $adResponse = PylesosService::get($adUrl, [], Config::getEnv());
                    $adDocument = new Crawler($adResponse->getResponse());

                    return $this->parseAd($adDocument, $category, $adUrl);
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
        $nextPageNumber = $this->getNextPageNumber($categoryDocument, $category, $url, $pageNumber);
        if ($nextPageNumber && $nextPageNumber <= 10) {
            $nextUrl = $this->getNextPageUrl($categoryDocument, $category, $url, $pageNumber);
            if ($nextUrl) {
                Logger::debug('Parsing next page ' . $nextUrl);
                $this->parseCategory($category, $nextUrl, $nextPageNumber + 1, $logContent);
            } else {
                Logger::warning('Not found next page on url ' . $nextUrl);
            }

        } else {
            Logger::warning('Ignored next page number ' . $nextPageNumber);
        }
    }
}