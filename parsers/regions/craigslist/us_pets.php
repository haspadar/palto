<?php

use Palto\Categories;
use Palto\Config;
use Palto\Regions;
use Pylesos\PylesosService;
use Symfony\Component\DomCrawler\Crawler;
use Palto\Logger;

require realpath(dirname(__DIR__) . '/../../') . '/vendor/autoload.php';

updateRegionIds();
updateRegionsAbbreviationUrls();

$donorUrl = 'https://www.craigslist.org/about/sites#US';
$level1Response = PylesosService::get($donorUrl, [], Config::getEnv());
$regionsDocument = new Crawler($level1Response->getResponse());
for ($columnId = 1; $columnId <= 4; $columnId++) {
    $regionsDocument->filter('.colmask')->eq(0)->filter('.box_' . $columnId)->each(
        function (Crawler $box, $i) use ($donorUrl) {
            $box->filter('h4')->each(function ($h4, $i) use ($box, $donorUrl) {
                $level1 = Regions::safeAdd(
                    [
                        'title' => ucwords($h4->text()),
                        'donor_url' => $donorUrl,
                    ]
                );
                Logger::debug('Added ' . ucwords($h4->text()));
                $box->filter('ul')->eq($i)->filter('li a')->each(function (Crawler $li) use ($level1) {
                    $level2 = Regions::safeAdd(
                        [
                            'title' => ucwords($li->text()),
                            'parent_id' => $level1->getId(),
                            'donor_url' => $li->attr('href') . '/search/pet'
                        ]
                    );
                    Logger::debug('Added ' . ucwords($li->text()) . ' (' . $level1->getTitle() . ')');
                    $level3Response = PylesosService::get($li->attr('href'), [], Config::getEnv());
                    $level3Document = new Crawler($level3Response->getResponse());
                    $level3Document->filter('.sublinks li a')->each(
                        function (Crawler $subLink, $i) use ($level2) {
                            Regions::safeAdd(
                                [
                                    'title' => ucwords($subLink->attr('title')),
                                    'abbreviation' => $subLink->text(),
                                    'parent_id' => $level2->getId(),
                                    'donor_url' => '/search' . $subLink->attr('href') . 'pet'
                                ]
                            );
                            Logger::debug('Added ' . ucwords($subLink->text()) . ' (' . $level2->getTitle() . ')');
                        }
                    );
                });
            });
        }
    );
}

function updateRegionsAbbreviationUrls() {
    /**
     * @var \Palto\Region $region
     */
    foreach (Regions::getRegions() as $region) {
        if ($region->getAbbreviation() && Regions::generateUrl($region->getAbbreviation()) != $region->getUrl()) {
            $url = Regions::generateUrl($region->getAbbreviation());
            (new \Palto\Model\Regions())->update(['url' => $url], $region->getId());
            Logger::warning('Updated region "' . $region->getTitle() . '" url from ' . $region->getUrl() . ' to ' . $url);
        }
    }
}

function updateRegionIds() {
    /**
     * @var \Palto\Ad $ad
     */
    foreach (\Palto\Ads::getAds(null, null) as $ad) {
        $url = $ad->getUrl();
        if (($region = Regions::getByUrl($url->getParts()[0]))
            || ($region = Regions::getByUrl($url->getSubDomains()[0]))
            || ($region = Regions::getByDonorUrl('https://' . $url->getSubDomains()[0] . '.craigslist.org/search/pet'))
            || ($region = Regions::getByTitle(ucfirst($url->getSubDomains()[0])))
        ) {
            if ($region->getId() != $ad->getRegion()->getId()) {
//                \Palto\Ads::update([
//                    'region_id' => $region->getId()
//                ], $ad->getId());
                Logger::warning('Updated ad ' . $ad->getId() . ': region_id=' . $region->getId() . ' instead region_id=' . $ad->getRegion()->getId());
            }
        }
    }
}