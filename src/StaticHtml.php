<?php
namespace Palto;

use Pylesos\Exception;
use Pylesos\Pylesos;
use Pylesos\PylesosService;

class StaticHtml
{
    const GENERATE_SCRIPT = 'generate_static_html.php';
    private string $domainUrl;
    private string $storagePath;
    private Palto $palto;

    /**
     * @param string $domainUrl
     * @param string $storagePath
     * @param Palto $palto
     * @example $staticHtml = new StaticHtml($palto->getPhpDomainUrl(), '/static', $palto);
     */
    public function __construct(string $domainUrl, string $storagePath, Palto $palto)
    {
        $this->domainUrl = $domainUrl;
        $this->storagePath = $storagePath;
        $this->palto = $palto;
    }

    public function generate()
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $regions = $this->palto->getRegions(0, 0, 0, 0, 'level');
        $categories = $this->palto->getWithAdsCategories(0, 0, 0, 0, 'level');
        $regionsCategoriesUrls = ['/'];
        foreach (array_merge([$this->palto->getDefaultRegion()], $regions) as $region) {
            $regionUrl = $this->palto->generateRegionUrl($region);
            $regionAdsCount = $this->palto->getAdsCount(0, $region['id']);
            $regionAdsPagesCount = $this->palto->calculatePagesCount($regionAdsCount);
            for ($pageNumber = 1; $pageNumber <= $regionAdsPagesCount; $pageNumber++) {
                $regionsCategoriesUrls[] = $pageNumber == 1 ? $regionUrl : $regionUrl . '/' . $pageNumber;
            }

            foreach ($categories as $category) {
                $categoryUrl = $this->palto->generateCategoryUrl($category, $region);
                $categoryAdsCount = $this->palto->getAdsCount($category, $region['id']);
                $categoryAdsPagesCount = $this->palto->calculatePagesCount($categoryAdsCount);
                for ($pageNumber = 1; $pageNumber <= $categoryAdsPagesCount; $pageNumber++) {
                    $regionsCategoriesUrls[] = $pageNumber == 1 ? $categoryUrl : $categoryUrl . '/' . $pageNumber;
                }
            }
        }

        $adsCount = $this->palto->getAdsCount(0, 0);
        $urlsCount = count($regionsCategoriesUrls) + $adsCount;
        $urlCounter = 0;
        foreach ($regionsCategoriesUrls as $url) {
            $this->saveUrlResponseToFile($url, ++$urlCounter, $urlsCount);
        }

        $adsPagesCount = $this->palto->calculatePagesCount($adsCount);
        for ($pageNumber = 1; $pageNumber <= $adsPagesCount; $pageNumber++) {
            $offset = ($pageNumber - 1) * $this->palto->getAdsLimit();
            $ads = $this->palto->getAds(0, 0, $this->palto->getAdsLimit(), $offset);
            foreach ($ads as $ad) {
                $this->saveUrlResponseToFile($this->palto->generateAdUrl($ad), ++$urlCounter, $urlsCount);
            }
        }

        $this->addAssetsLinks();
        $executionTime->end();
        $this->palto->getLogger()->info(
            'Generated ' . $urlsCount . ' static pages for ' . $executionTime->get()
        );
    }

    private function saveUrlResponseToFile(string $url, int $counter, int $count)
    {
        $directoryFullPath = $this->palto->getRootDirectory() . $this->storagePath . $url;
        if (!file_exists($directoryFullPath)) {
            mkdir($directoryFullPath, 0777, true);
        }

        file_put_contents(
            $directoryFullPath . '/index.html',
            PylesosService::getWithoutProxy($url, $this->palto->getEnv())
        );
        $this->palto->getLogger()->debug('Saved static html ' . $directoryFullPath . '/index.html', [
            'progress' => $counter . '/' . $count
        ]);
    }

    private function addAssetsLinks()
    {
        $publicDirectories = ['css', 'img', 'js', 'sitemaps'];
        foreach ($publicDirectories as $publicDirectory) {
            $fullPublicPath = $this->palto->getRootDirectory() . '/public/' . $publicDirectory;
            $fullStaticPath = $this->palto->getRootDirectory() . $this->storagePath . '/';
            $command = 'ln -s ' . $fullPublicPath . ' ' . $fullStaticPath;
            `$command`;
        }
    }
}