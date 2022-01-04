<?php

namespace Palto;

use Pylesos\PylesosService;
use Pylesos\Scheduler;
use Symfony\Component\DomCrawler\Crawler;

abstract class AdsParser
{
    protected const IS_PROXY_REQUIRED = true;

    abstract protected function findAds(Crawler $categoryDocument);
    abstract protected function findAdUrl(Crawler $resultRow, Category $category): ?Url;

    public function run(string $file)
    {
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
                        $this->parseCategory($category, $category->getDonorUrl(), $logContent);
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

    private function parseCategory(Category $category, Url $url, array $logContent = [])
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
            $adUrl = $this->findAdUrl($resultRow);
            if (!$adUrl) {
                Logger::error('Url not parsed: ' . $resultRow->outerHtml());
            } elseif (!Ads::getByUrl($adUrl)) {
                $isAdded = Parser::safeTransaction(function () use ($category, $adUrl) {
                    $adResponse = PylesosService::get($adUrl, [], Config::getEnv());
                    $adDocument = new Crawler($adResponse->getResponse());

                    return $this->parseAd($adDocument, $category, $adUrl);
                });

                if (!is_bool($isAdded)) {
                    Logger::debug('Skipped wrong ad with url ' . $adUrl->getFull());
                }

                if ($isAdded) {
                    $addedAdsCount++;
                }
            } else {
                Logger::debug('Ad with url ' . $adUrl . ' already exists');
            }
        });
        Logger::info('Added ' . $addedAdsCount . ' ads from page ' . $url, $extendedLogContext);
        if (Parser::hasNextPageLinkTag($categoryDocument) && Parser::getNextPageNumber($categoryDocument) <= 10) {
            Logger::debug('Parsing next page ' . Parser::getNextPageUrl($categoryDocument));
            $this->parseCategory($category, Parser::getNextPageUrl($categoryDocument), $logContent);
        }
    }
}