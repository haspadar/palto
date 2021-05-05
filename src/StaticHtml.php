<?php
namespace Palto;

use Pylesos\Exception;
use Pylesos\Pylesos;
use Pylesos\PylesosService;

class StaticHtml
{
    const GENERATE_SCRIPT = 'generate_static_html.php';
    private string $domainUrl;
    private string $path;
    private Palto $palto;

    public function __construct(string $domainUrl, string $path, Palto $palto)
    {
        $this->domainUrl = $domainUrl;
        $this->path = $path;
        $this->palto = $palto;
    }

    public function generate()
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $regions = $this->palto->getRegions(0, 0, 0, 0, 'level');
        $categories = $this->palto->getCategories(0, 0, 0, 0, 'level');
        $regionsCategoriesUrls = ['/'];
        foreach ($regions as $region) {
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

        $executionTime->end();
        $this->palto->getLogger()->info(
            'Generated ' . $urlsCount . ' static pages for ' . $executionTime->get()
        );
    }

    private function saveUrlResponseToFile(string $url, int $counter, int $count)
    {
        $directoryFullPath = $this->palto->getRootDirectory() . $this->path . $url;
        if (!file_exists($directoryFullPath)) {
            mkdir($directoryFullPath, 0777, true);
        }

        file_put_contents(
            $directoryFullPath . '/index.html',
            $this->download($this->domainUrl . $url)
        );
        $this->palto->getLogger()->debug('Saved static html ' . $directoryFullPath . '/index.html', [
            'progress' => $counter . '/' . $count
        ]);
    }

    private function download(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $headers = [];
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }
}