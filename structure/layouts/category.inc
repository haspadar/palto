<?php

use Palto\Palto;

$palto = Palto::getInstance();
$categoryWithChildrenIds = array_merge(
    [$palto->getCurrentCategory()['id']],
    array_column($palto->getCurrentCategory()['children'], 'id')
);
$count = $palto->getAdsCount(
    $categoryWithChildrenIds,
    $palto->getCurrentRegion()['id']
);
$palto->initPagination($count);
$palto->setTitle(
    implode(' - ', array_column($palto->getCurrentCategory()['parents'], 'title'))
    . ' - '
    . $palto->getCurrentCategory()['title']
);
$palto->setDescription(
    '🇩Category Description: '
    . implode(' - ', array_column($palto->getCurrentCategory()['parents'], 'title'))
    . ' - ' . $palto->getCurrentCategory()['title']
);
?>
<?php require_once 'header.inc' ?>
<div id="col-mid">
    <div class="col-mid-inner">
        <div class="cmi-1">
            <div class="cmi-1-1">
                <div class="block bread">
                    <div class="bl1">
                        <div class="b1" itemscope itemtype="http://schema.org/BreadcrumbList">
                            <?php foreach ($palto->getCurrentCategoryBreadcrumbUrls() as $breadcrumbKey => $breadcrumbItem) :?>
                                <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                    <a itemprop="item" href="<?=$breadcrumbItem['url']?>" class="bread">
                                        <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
                                    </a>
                                    <meta itemprop="position" content="<?=$breadcrumbKey + 1?>"/>
                                </span>
                                <span class="sep">»</span>
                            <?php endforeach;?>

                            <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name"><?= $palto->getCurrentCategory()['title'] ?></span><meta itemprop="position" content="<?=$breadcrumbKey ?? 0 + 1?>"/></span>
                        </div>
                    </div>
                </div>
                <div class="main-ob">
                    <div class="block">
                        <div class="bl1">
                            <h1><?= $palto->getCurrentCategory()['title']?></h1>
                            <ul class="adap">
                                <?php foreach ($palto->getCategories($palto->getCurrentCategory()['id']) as $childCategory) :?>
                                    <li><a href="<?=$palto->generateCategoryUrl($childCategory)?>">
                                            <?=$childCategory['title']?>
                                        </a>
                                    </li>
                                <?php endforeach?>
                            </ul>
                            <div class="filter-level">
                                <div class="fl1">
                                    <div class="reg-sel">
                                        Region: <a class="ch-region bold" href="<?=$palto->generateRegionUrl($palto->getCurrentRegion())?>">
                                            <?= $palto->getCurrentRegion()['title'] ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block">
                        <div class="bl1">
                            <?php
                            foreach ($palto->getAds(
                                $categoryWithChildrenIds,
                                $palto->getCurrentRegion()['id'] ?? 0,
                                $palto->getAdsLimit(),
                                $palto->getAdsOffset()
                                ) as $adIndex => $ad) :?>

                            <div class="ob-block">
                                <div class="img">
                                    <div class="i0">
                                        <div class="i1">
                                            <a href="<?= $palto->generateAdUrl($ad) ?>">
                                                <?php if ($ad['images']) : ?>
                                                    <img src="<?= $ad['images'][0]['small'] ?>" alt="<?=$ad['title']?>"/>
                                                <?php endif; ?>

                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="bod">
                                    <div class="t"><a href="<?= $palto->generateAdUrl($ad) ?>" class="bread">
                                            <span><?= $ad['title'] ?></span></a></div>
                                    <div class="d"><?= $palto->generateShortText($ad['text']) ?></div>
                                    <div class="br">
                                        <?php foreach ($palto->getAdBreadcrumbUrls($ad) as $breadcrumbItem) :?>
                                            <a href="<?= $breadcrumbItem['url'] ?>" class="bread"><?= $breadcrumbItem['title'] ?></a>
                                        <?php endforeach;?>
                                    </div>
                                    <div class="p">
                                        <!--noindex-->
                                        <span class="price2"><b><?= $ad['currency'] ?> <?= $ad['price'] ?></b> </span>

                                        <!--/noindex-->
                                    </div>
                                    <div>
                                        <?php $region = $palto->getRegion($ad['region_id'])?>
                                        <a href="<?=$region['url']?>">
                                            <?= $region['title'] ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php if (in_array($adIndex + 1, [5, 15])) : ?>
<!--                                Counter-->
                            <?php elseif (in_array($adIndex + 1, [2, 10, 21])) : ?>
                            <!--                                Counter-->
                            <?php endif; ?>
                            <?php endforeach;?>

                            <?php foreach ($palto->getPaginationUrls() as $page) :?>
                                <?php if ($page['url']) :?>
                                    <a href="<?=$page['url']?>"><?=$page['title']?></a>
                                <?php else :?>
                                    <?=$page['title']?>
                                <?php endif;?>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.inc';