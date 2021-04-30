<?php

use Palto\Palto;
use Pylesos\PylesosService;
use simplehtmldom\HtmlDocument;

require 'vendor/autoload.php';

$palto = new Palto();
$level1Response = PylesosService::download('https://losangeles.craigslist.org/', $palto->getEnv());
$level1Document = new HtmlDocument($level1Response->getResponse());
foreach ($level1Document->find('.col') as $col) {
    $leve1Title = strip_tags($col->find('h3 a span', 0)->innertext);
    $level1DonorUrl = $col->find('a', 0)->href;
    $level1Url = transformUrl($level1DonorUrl);
    if ($leve1Title !== 'discussion forums') {
        echo '-' . $leve1Title . '(' . $level1Url . ')' . PHP_EOL;
        $palto->getDb()->insertIgnore('categories', [
            'title' => $leve1Title,
            'donor_url' => $level1DonorUrl,
            'level' => 1,
            'url' => $level1Url,
            'create_time' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
        $level1Id = $palto->getDb()->insertId();
        foreach ($col->find('li') as $level2) {
            $leve2Title = strip_tags($level2->find('a span', 0)->innertext);
            $level2DonorUrl = $level2->find('a', 0)->href;
            $level2Url = transformUrl($level2DonorUrl);
            echo '--' . $leve2Title . '(' . $level2Url . ')' . PHP_EOL;
            $palto->getDb()->insertIgnore('categories', [
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

function transformUrl($url) {
    return str_replace('/', '_', substr($url, 1, -1));
}