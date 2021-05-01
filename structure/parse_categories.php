<?php

use Palto\Palto;
use Pylesos\PylesosService;
use simplehtmldom\HtmlDocument;

const DONOR_URL = 'https://losangeles.craigslist.org';

require 'vendor/autoload.php';

$palto = new Palto();
$level1Response = PylesosService::download(DONOR_URL . '/', $palto->getEnv());
$level1Document = new HtmlDocument($level1Response->getResponse());
foreach ($level1Document->find('.col') as $col) {
    $leve1Title = strip_tags($col->find('h3 a span', 0)->innertext);
    $level1DonorUrl = $col->find('a', 0)->href;
    $level1Url = $palto->findCategoryUrl($leve1Title, 1);
    if ($leve1Title !== 'discussion forums') {
        $palto->getLogger()->debug($leve1Title . '(' . $level1Url . ')');
        $level1Id = $palto->getCategoryId([
            'title' => $leve1Title,
            'donor_url' => $level1DonorUrl,
            'level' => 1,
            'url' => $level1Url,
            'create_time' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
        foreach ($col->find('li') as $level2) {
            $leve2Title = strip_tags($level2->find('a span', 0)->innertext);
            $level2DonorUrl = $level2->find('a', 0)->href;
            $level2Url = $palto->findCategoryUrl($leve2Title, 2);
            $palto->getLogger()->debug($leve2Title . '(' . $level2Url . ')');
            $palto->getCategoryId([
                'title' => $leve2Title,
                'parent_id' => $level1Id,
                'donor_url' => $level2DonorUrl,
                'level' => 2,
                'url' => $level2Url,
                'create_time' => (new DateTime())->format('Y-m-d H:i:s')
            ]);
        }
    }
}